<?php
/**
 * This file contains the FormatterFactory. It is a singleton which creates an object of the right formatprinter
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */
namespace tdt\core\formatters;

use tdt\core\formatters\FormatterFactory;
use tdt\framework\ContentNegotiator;
use tdt\framework\TDTException;

/**
 * This class will provide the correct printers (Xml,Kml,php,...)
 */
class FormatterFactory{

    private $format;

    private static $formatterfactory;

    public static function getInstance($urlformat = ""){           
	if(!isset(self::$formatterfactory)){
	    self::$formatterfactory = new FormatterFactory($urlformat);
	}
	return self::$formatterfactory;
    }    

    /**
     * sets the requested format in the factory from the request URL
     * @param string $urlformat The format of the request i.e. json,xml,....
     */
    public function setFormat($urlformat){
        //We define the format like this:
        // * Check if $urlformat has been set
        //   - if not: probably something fishy happened, set format as error for logging purpose
        //   - else if is about: do content negotiation
        //   - else check if format exists 
        //        × throw exception when it doesn't
        //        × if it does, set $this->format with ucfirst

        //first, let's be sure about the case of the format
        $urlformat = ucfirst(strtolower($urlformat));
       
        if(strtolower($urlformat) == "about" || $urlformat == "" ){ //urlformat can be empty on SPECTQL query
            
            $cn = new ContentNegotiator();
            $format = $cn->pop();
            while(!$this->formatExists($format) && $cn->hasNext()){
                $format = $cn->pop();
                if($format == "*"){
                    $format == "Xml";
                }
            }
            if(!$this->formatExists($format)){                
                throw new TDTException(451,array($format)); // could not find a suitible format
            }
            $this->format = $format;            
            //We've found our format through about, so let's set the header for content-location to the right one
            //to do this we're building our current URL and changing .about in .format
            $format= strtolower($this->format);
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"])) {$pageURL .= "s";}
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }
            $contentlocation = str_replace(".about", "." . $format, $pageURL);
            header("Content-Location:" . $contentlocation);
        }else if($this->formatExists($urlformat)){
            $this->format = $urlformat;
        }else{            
            throw new TDTException(451,array($urlformat));
        }
        
    }
    

    /**
     * The constructor will get the right format and will decide which printer should be used to print the object.
     */
    private function __construct($urlformat = ""){
        $this->setFormat($urlformat);
    }

    private function formatExists($format){           
        return file_exists("custom/formatters/". $format . "Formatter.class.php") || file_exists("custom/formatters/visualizations/". $format . "Formatter.class.php");
    }

    /**
     * Returns the format that has been set by the request
     * @return A format object
     */
    public function getFormat(){
	return $this->format;
    }

    /**
     * This function will return a printer instance of a certain type.
     * @param string $rootname This is needed for some printers.
     * @param string $format   This string will be used to classload the correct printer.
     * @param Mixed  $objectToPrinter This is the object that will be printed.
     * @return Correct printer according to the $format parameter.
     */
    public function getPrinter($rootname, &$objectToPrint){        
	$callback = null;
	//this is a fallback for jsonp - if callback is given, just return jsonp anyway
	if(($this->format == "Json" || $this->format == "Jsonp") && isset($_GET["callback"])){
	    $callback = $_GET["callback"];
	    $this->format = "Jsonp";            
	    include_once("custom/formatters/". $this->format . "Formatter.class.php");
            $format=$this->format."Formatter";
	    return new $format($rootname,$objectToPrint,$callback);
	}
        $format=$this->format."Formatter";
        // Before this is done, a check on the existence of the format has already been done, so we now we can 
        // automatically include the visualization format if the format isn't found in the formatters folder.
	if(file_exists("custom/formatters/". $this->format . "Formatter.class.php")){
            include_once("custom/formatters/". $this->format . "Formatter.class.php");
        }else{
            include_once("custom/formatters/visualizations/". $this->format . "Formatter.class.php");
        }
	return new $format($rootname, $objectToPrint);
    }
    
    
    /**
     * This will fetch all the documentation from the formatters and put it into the documentation visitor
     * @return The documentation object from the formatters
     */
    public function getFormatterDocumentation(){
        $doc = array();
        //open the custom directory and loop through it
        if ($handle = opendir('custom/formatters')) {
            while (false !== ($formatter = readdir($handle))) {
                //if the object read is a directory and the configuration methods file exists, then add it to the installed formatters
                if ($formatter != "." && $formatter != ".." && file_exists("custom/formatters/" . $formatter)) {
                    $boom = explode(".",$formatter);
                    $formatterclass = $boom[0];
                    if(preg_match("/(.*)..php/si",$formatter,$match)){
                        include_once("custom/formatters/" . $formatter);
                        if(is_subclass_of($formatterclass, "AFormatter")){
                            $doc[$match[1]] = $formatterclass::getDocumentation();
                        }
                    }
                }   
            }
            closedir($handle);
        }
        return $doc;
    }

    /**
     * This will fetch all the documentation from the formatters and put it into the documentation visitor
     * @return The documentation object from the formatters
     */
    public function getVisualizationDocumentation(){
        $doc = array();
        //open the custom directory and loop through it
        if ($handle = opendir('custom/formatters/visualizations')) {
            while (false !== ($formatter = readdir($handle))) {
                //if the object read is a directory and the configuration methods file exists, then add it to the installed formatters
                if ($formatter != "." && $formatter != ".." && file_exists("custom/formatters/visualizations/" . $formatter)) {
                    $boom = explode(".",$formatter);
                    $formatterclass = $boom[0];
                    if(preg_match("/(.*)..php/si",$formatter,$match)){
                        include_once("custom/formatters/visualizations/" . $formatter);
                        if(is_subclass_of($formatterclass, "AFormatter")){
                            $doc[$match[1]] = $formatterclass::getDocumentation();
                        }
                    }
                }   
            }
            closedir($handle);
        }
        return $doc;
    }
}
?>

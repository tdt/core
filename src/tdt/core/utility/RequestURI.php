<?php
/**
 * This class will provide you a tool to ask for URI parameters
 *
 * @package The-DataTank
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 */

namespace tdt\core\utility;

use tdt\core\utility\Config;


class RequestURI{
    private static $instance;

    private $protocol, $host, $port, $package,$resource, $filters, $format, $GETParameters;

    private function __construct(){
        $this->protocol = 'http';
        if (!empty($_SERVER['HTTPS'])) {
            if($_SERVER['HTTPS'] == 'on') {
                $this->protocol .= "s";
            }
        }
        $this->host = $_SERVER['SERVER_NAME'] . "/";
        $this->port = $_SERVER["SERVER_PORT"];

        $requestURI = $_SERVER["REQUEST_URI"];
        //if a SUBDIR has been set in the config, remove this from here
        if(Config::get("general","subdir") != ""){            
            $requestURI = preg_replace("/". str_replace('/','\/',Config::get("general","subdir")) ."/si","", $requestURI, 1);
        }

        //Now for the hard part: parse the REQUEST_URI
        //This can look like this: /package/resource/identi/fiers.json

        $path = explode("/",$requestURI);

        $i = 0;
        //shift the path chunks as long as they exist and add them to the right variable
        while(sizeof($path) > 0){
            if($i == 0){
                if(empty($path[0])){
                    $this->package = $path[1];
                    array_shift($path);
                }else{
                    $this->package = $path[0];
                }
            }elseif($i == 1){
                $this->resource = $path[0];
                //if this is the last element in the array
                //we might get the format out of it
                $resourceformat = explode(".",$this->resource);
                if(sizeof($path) == 1 && sizeof($resourceformat)>1){
                    $this->format =  array_pop($resourceformat);
                    $this->resource = implode(".",$resourceformat);
                }
            }elseif($i > 1){
                //if this is the last element in the array
                //we might get the format out of it
                $arrayformat = explode(".",$path[0]);
                if(sizeof($path) == 1 && sizeof($arrayformat) > 1){
                    $this->format =  array_pop($arrayformat);
                    $this->filters[] = implode(".",$arrayformat);
                }else{
                    $this->filters[] = $path[0];
                }
            }
            array_shift($path);
            $i++;
        }
        //we need to sort all the GET parameters, otherwise we won't have a unique identifier for for instance caching purposes
        if (is_null($_GET)){
            $this->GETParameters = $_GET;
            asort($GETParameters);
        }
    }

    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new RequestURI();
        }
        return self::$instance;
    }

    public function getProtocol(){
        return $this->protocol;
    }

    public function getHostName(){
        return $this->host;
    }

    public function getSubDir(){
        return Config::get("general","subdir");
    }

    public function getPackage(){
        return $this->package;
    }

    public function getResource(){
        return $this->resource;
    }

    public function getFilters(){
        if(!is_null($this->filters)){
            return $this->filters;
        }
        return array();
    }

    public function getGET(){
        if(!is_null($this->GETParameters)){
            return $this->GETParameters;
        }
        return array();
    }

    public function getGivenFormat(){
        return $this->format;
    }

    public function getRealWorldObjectURI(){
        $URI = $this->protocol . "://" . $this->host . $this->getSubDir() . $this->package . "/";
        $URI .= $this->getResourcePath();

        return $URI;
    }

    public function getResourcePath(){
        $URI = $this->resource;
        if(isset($this->filters) && !is_null($this->filters)){
            $URI .= "/";
            $URI .= implode("/", $this->filters);
        }

        //Remove trailing slash
        if (strripos($URI, '/') == strlen($URI) - 1)
            $URI = substr($URI, 0, strlen($URI) - 1);

        return $URI;
    }

    public function getURI(){
        $URI = $this->protocol . "://" . $this->host . $this->getSubDir() . $this->package . "/" . $this->resource;
        if(isset($this->filters) && !is_null($this->filters)){
            $URI .= "/";
            $URI .= implode("/", $this->filters);
        }
        if($this->format != ""){
            $URI .= "." . $this->format;
        }
        if(sizeof($this->GETParameters) > 0){
            $URI .= "?";
            foreach($this->GETParameters as $key => $value){
                $URI .= $key . "=" . $value . "&";
            }
            $URI = rtrim($URI,"&");
        }
        return $URI;
    }
}

?>

<?php
/**
 * This controller will redirect the user for content negotiation
 * @package The-Datatank/controllers
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */


class RedirectController extends AController{
    
    public function __construct() {
        AutoInclude::register("RequestURI", "cores/core/RequestURI.class.php");        
    }
    
    /**
     * You cannot get a real-world object, only its representation. Therefore we're going to redirect you to .about which will do content negotiation.
     */
    function GET($matches){

        //get the current URL
        $ru = RequestURI::getInstance();
        $pageURL = $ru->getURI();
        $pageURL = rtrim($pageURL, "/");
        //add .about before the ?
        if (sizeof($_GET) > 0) {
            $pageURL = str_replace("?", ".about?", $pageURL);
            $pageURL = str_replace("/.about", ".about", $pageURL);
        } else {
            $pageURL .= ".about";
        }

        header("HTTP/1.1 303 See Other");
        header("Location:" . $pageURL);    
    }

    function HEAD($matches){
        $this->GET($matches);
    }

    function POST($matches){
        //get the current URL
        $ru = RequestURI::getInstance();
        $pageURL = $ru->getURI();
        throw new TDTException(450, array("POST",$pageURL));
    }
    
    function PUT($matches){
        //get the current URL
        $ru = RequestURI::getInstance();
        $pageURL = $ru->getURI();
        throw new TDTException(450, array("PUT",$pageURL));
    }
    
    function DELETE($matches){
        //get the current URL
        $ru = RequestURI::getInstance();
        $pageURL = $ru->getURI();
        throw new TDTException(450, array("DELETE",$pageURL));
    }

    /**
     * You cannot use patch a representation
     */
    public function PATCH($matches) {
        //get the current URL
        $ru = RequestURI::getInstance();
        $pageURL = $ru->getURI();
        throw new TDTException(450, array("PATCH",$pageURL));
    }

}

?>

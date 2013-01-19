<?php

/*
 * Class inheriting from the AController from tdt\framework
 * and adding some functionality specifically for Core controllers
 */

namespace tdt\core\controllers;

use tdt\framework\Cache\Cache;
use tdt\core\utility\Config;
use RedBean_Facade as R;

class AController{
    /*
     * installation variables
     */

    protected $hostname;
    protected $subdir;

    /*
     * back-end variables
     */
    protected $dbhost;
    protected $dbname;
    protected $dbsystem;
    protected $dbuser;
    protected $dbpassword;
    
    private $format_through_url;

    public function __construct() {
        $this->hostname = Config::get("general", "hostname");
        $this->subdir = Config::get("general", "subdir");

        $this->dbhost = Config::get("db", "host");
        $this->dbname = Config::get("db", "name");
        $this->dbsystem = Config::get("db", "system");
        $this->dbuser = Config::get("db", "user");
        $this->dbpassword = Config::get("db", "password");
        
        $this->format_through_url = "";
    }
        
    public function setFormat($format){
        $this->format_through_url = $format;
    }
    
    /**
     * Helper function for the language. If you want better language resolution, just use the language negotiator yourself as a stack, as documented in the source code.
     */
    protected function getLang(){
        $ln = new LanguageNegotiator(Config::get("general","defaultlanguage"));
        return $ln->hasNext();
    }
    
    protected function getFormat($formats=null){
        if($formats == null){
            $formats = array("turtle","ntriples","rdfxml","xml", "csv","json");
        }
        
        //always give format set throught the URL the upperhand
        if($this->format_through_url !== ""){
            return $this->format_through_url;
        }else{
            $cn = new ContentNegotiator(Config::get("general","defaultformat"));
            Log::getInstance()->logInfo("Getting content negotiation array");
            $format=$cn->pop();
            while(!in_array($format,$formats) && $cn->hasNext()){
                $format = $cn->pop();
            }
            return $format;
        }
    }

    protected function getBaseURL($str = ""){
        return Config::get("general","hostname") . Config::get("general","subdir") . $str;
    }

    protected function isBasicAuthenticated(){
        if(Config::get("general","auth","enabled")){
            return isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == Config::get("general","auth","api_user") && $_SERVER['PHP_AUTH_PW'] == Config::get("general","auth","api_passwd");
        }else{
            return true;
        }
    }
    
    protected function initializeDatabaseConnection(){
        R::setup($this->dbsystem . ":host=" . $this->dbhost . ";dbname=" . $this->dbname, $this->dbuser, $this->dbpassword);
    }


    protected function clearCachedDocumentation(){
        $c =Cache::getInstance();
        $c->delete($this->hostname . $this->subdir . "documentation");
        $c->delete($this->hostname . $this->subdir . "descriptiondocumentation");
        $c->delete($this->hostname . $this->subdir . "admindocumentation");
        $c->delete($this->hostname . $this->subdir . "packagedocumentation");
    }

}

?>

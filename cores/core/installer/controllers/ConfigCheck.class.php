<?php
/**
 * Installation step: config file check
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 */

class ConfigCheck extends InstallController {
    
    public function index() {
        $data = array();
        $basePath = dirname(__FILE__)."/../../";
        
        // check config file existence
        //TODO delete if else structure
        if(false) {
            $data["config_exists"] = FALSE;
            $this->installer->nextStep(FALSE);
        }
        else {
            
            $data["config_exists"] = TRUE;
            
            //get the keys value pairs from the configuration file
            //since the configuration is now divided into categories
            //names can be duplicated, therefore we will add the category in front of the key                        
            
            // get the TDTCore variables
            $tests = Config::get("core");            
            $tests = array_merge($tests, Config::get("general"));                        
            $tests = array_merge($tests, Config::get("auth"));
            
            foreach(Config::get("cache") as $key => $value){
                $key = "cache_" . $key;
                $tests[$key] = $value;
            }                        
            
            
            foreach($tests as $key=>$value) {
                // defaults
                $status = "passed";
                $message = "";
                
                switch($key) {
                    case "hostname":                        
                        $pieces = parse_url($value);                                              
                        if($pieces["host"] != $_SERVER["SERVER_NAME"]) {
                            $status = "warning";
                            $message = "hostname_no_match";
                        }
                        elseif($pieces["scheme"] != "https") {
                            $status = "warning";
                            $message = "hostname_no_https";
                        }
                        break;
                    case "cache_system":
                        $cacheClass = "TDT".$value;
                        $aspect = dirname(__DIR__)."/../../../framework/caching/TDT".Config::get("cache","system").".class.php";                        
                        if(!file_exists($aspect)) {
                            $status = "failed";
                            $message = "cache_not_supported";
                        }
                        elseif($value != "MemCache") {
                            $status = "warning";
                            $message = "cache_no_memcache";
                        }
                        break;
                    case "cache_host":
                        break;
                    case "cache_port":                        
                        $aspect = __DIR__."/../../aspects/caching/TDT".Config::get("cache","system").".class.php";
                        if(!file_exists($aspect)) {
                            $status = "warning";
                            $message = "cache_not_tested";
                        }
                        elseif(Config::get("cache","system") == "MemCache" && !class_exists("Memcache")) {
                            $status = "error";
                            $message = "memcache_not_installed";
                        }
                        elseif(Config::get("cache","system") != "NoCache") {
                            include_once($basePath."/aspects/caching/Cache.class.php");
                            $cache = Cache::getInstance();
                            
                            $testKey = "temp_".uniqid();
                            $testValue = uniqid();
                            
                            $cache->set($testKey, $testValue, 1);
                            if($cache->get($testKey) != $testValue) {
                                $status = "failed";
                                $message = "cache_wrong_credentials";
                            }
                        }
                        break;
                    case "subdir":
                        // guess the subdir
                        $dirs = explode("/",str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']));
                        
                        // remove empty and install dir
                        foreach($dirs as $k=>$dir)
                            if(!$dir || $dir == "installer" || $dir== "cores" || $dir == "core")
                                unset($dirs[$k]);
                        
                        if($dirs)
                            $subdir = implode("/", $dirs)."/";
                        else
                            $subdir = "";
                        
                        if(!$value && $value != $subdir) {
                            $status = "failed";
                            $message = "subdir_detected";
                        }
                        elseif($value != $subdir) {
                            $status = "failed";
                            $message = "subdir_wrong";
                        }
                        break;
                    case "dbsystem":
                        break;
                    case "dbuser":
                        break;
                    case "dbpassword":
                        $status = "skipped";
                        break;
                    case "api_user":                       
                        if(!$value) {
                            $status = "failed";
                            $message = "api_no_user";
                        }
                        break;
                    case "api_passwd":
                        $pwd = $value;
                        $value ="";
                        for($i=0; $i<strlen($pwd); $i++)
                            $value .= "*";
                        
                        if(!$value) {
                            $status = "failed";
                            $message = "api_no_pass";
                        }
                        elseif(strlen($value)<6) {
                            $status = "failed";
                            $message = "api_short_pass";
                        }
                        break;
                }

                // don't allow next step on error
                if($status=="failed")
                    $this->installer->nextStep(FALSE);
                
                $tests[$key] = array("value"=>$value, "status"=>$status, "message"=>$message);
            }
            
            $data["tests"] = $tests;
        }
        
        $this->view("config", $data);
    }
    
}
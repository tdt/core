<?php
/**
 * This class will log a request into the database
 * @package The-Datatank/aspects/logging
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */ 
 
class RequestLogger{
    /**
     * This function implements the logging part of the RequestLogger functionality.
     */
    public static function logRequest($package="", $resource="", $parameters="",$RESTparameters = "") {
        R::setup(Config::get("core", "dbsystem") . ":host=" . Config::get("core", "dbhost") . ";dbname=" . Config::get("core", "dbname"), Config::get("core", "dbuser"), Config::get("core", "dbpassword"));            
	//an instance of printerfactory so we can check the format
	$ff = FormatterFactory::getInstance();
        //an instance of RequestURI
        $URI = RequestURI::getInstance();
        
        $request = R::dispense('requests');
        $format = $ff->getFormat();

        $ip = RequestLogger::getIpAddress();
        $request->ip = $ip;
        $request->time = time();
        if(isset($_SERVER['HTTP_USER_AGENT'])){    
            $request->user_agent = $_SERVER['HTTP_USER_AGENT'];
        }else{
            $request->user_agent = "";
        }

        $request->request_method = $_SERVER['REQUEST_METHOD'];        

        $request->url_request = $URI->getURI();
        if($package == ""){
            $request->package = $URI->getPackage();
        }
        else{
            $request->package = $package;
        }
        if($resource == "")
            $request->resource = $URI->getResource();
        else
            $request->resource = $resource;
        $request->format = $format;
        if($parameters == ""){
            $request->requiredparameter = implode(";",$URI->getFilters());
        }
        else{
            $request->requiredparameter = implode(";",$parameters);
        }

        if($RESTparameters == "" && !is_null($URI->getGET())){
            $request->allparameters = implode(";",$URI->getGET());
        }else if($RESTparameters != ""){
            $request->allparameters = implode(";",$RESTparameters);
        }else{
            $request->allparameters = "";
        }
        
        $result = R::store($request);
    }

    private static function getIpAddress() {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }
}
?>

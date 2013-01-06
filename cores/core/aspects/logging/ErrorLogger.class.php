<?php
/**
 * This is an errorhandler, it will do everything that is expected when an error occured.
 *
 * @package The-Datatank/aspects/logging
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <Jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */


/**
 * This function is called when an unexpected error(non-exception) occurs in receiver.php.
 * @param integer $number Number of the level of the error that's been raised.
 * @param string  $string Contains errormessage.
 * @param string  $file   Contains the filename in which the error occured.
 * @param integer $line   Represents the linenumber on which the error occured.
 * @param string  $context Context is an array that points to the active symbol table at the point the error occurred. In other words, errcontext will contain an array of every variable that existed in the scope the error was triggered in. User error handler must not modify error context.
 */
function wrapper_handler($number,$string,$file,$line,$context){
    $error_message = $string . " on line " . $line . " in file ". $file . ".";
    $exception = new TDTException(500,array($error_message));
    ErrorHandler::logException($exception);
    //Exit when we received 1 error. No need to continue
    exit(0);
}

/**
 * This class handles and logs errors and exceptions.
 */
class ErrorHandler{

    /**
     * This functions logs the exception.
     * @param Exception $e Contains an Exception class.
     */
    public static function logException($e) {
        //HTTP Header information
        header("HTTP/1.1 ". $e->getCode() . " " . $e->getMessage());
        //In the body, put the message of the error
        echo $e->getMessage();

        //and store it to the DB
        ErrorHandler::WriteToDB($e);
    }

    private static function WriteToDB(Exception $e) {
        R::setup(Config::get("core", "dbsystem") . ":host=" . Config::get("core", "dbhost") . ";dbname=" . Config::get("core", "dbname"), Config::get("core", "dbuser"), Config::get("core", "dbpassword"));            
        $URI = RequestURI::getInstance();
        
	//ask the printerfactory which format we should store in the db
	$ff = FormatterFactory::getInstance();
	$format = $ff->getFormat();

        // get the stack trace
        $trace = $e->getTrace();
        $trace = ErrorHandler::makePrettyTrace($trace);
        // get the linenumber of where the exception has occured
        $line = $e->getLine();
        // get the file of where the exception has occured
        $file = $e->getFile();

        $error = R::dispense('errors');
        $error->time = time();
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            $error->user_agent = $_SERVER['HTTP_USER_AGENT'];
        }else{
            $error->user_agent = "";
        }
        
        $error->format = $format;
        $error->url_request = $URI->getURI();
        $error->error_message = $e->getMessage();
        $error->error_code = $e->getCode();
        $error->trace = $trace;
        $error->line = $line;
        $error->file = $file;
        R::store($error);
    }

    private static function makePrettyTrace($trace){
        $traceString = "";
        foreach($trace as $traceEntry){
            unset($traceEntry["args"]);
            foreach($traceEntry as $key => $value){
                $traceString.= " " . $key . " = " . $value ." ;";
            }
            $traceString.= " ||";
        }
        return $traceString;
    }
}
?>
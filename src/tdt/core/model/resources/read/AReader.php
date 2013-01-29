<?php

/**
 * Abstract class for reading(fetching) a resource
 *
 * @package The-Datatank/model/resources/read
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\resources\read;

use tdt\negotiators\LanguageNegotiator;
use tdt\exceptions\TDTException;

abstract class AReader {

    protected static $DEFAULT_PAGE_SIZE = 50;

    public static $BASICPARAMS = array("callback", "filterBy", "filterValue", "filterOp","page_size","page");
    
    // package and resource are always the two minimum parameters
    protected $parameters = array();
    protected $requiredParameters = array();
    protected $package;
    protected $resource;
    protected $RESTparameters;

    public function __construct($package, $resource, $RESTparameters) {
        $this->package = $package;
        $this->resource = $resource;
        $this->RESTparameters = $RESTparameters;
    }

    /**
     * Gets the REST parameters of the request
     * @return array with the REST parameters.
     */
    public function getRESTParameters() {
        return $this->RESTparameters;
    }

    /**
     * Execution method of a reader, which reads a resource
     * @return \stdClass of a datasource
     */
    public function execute() {
        return $this->read();
    }

    /**
     * read method of a resource
     */
    abstract public function read();

    /**
     * Processes the parameters necessary to read a certain resource
     * @param array $parameters An array with the parameters passed with the GET request.
     */
    public function processParameters($parameters) {
        /*
         * set the parameters
         */
        foreach ($parameters as $key => $value) {
            $this->setParameter($key, $value);
        }
    }

    abstract protected function setParameter($key, $value);

    /**
     * Override this function if you want to limit language support
     */
    public function supportedLanguages() {
        return array();
    }

    /**
     * Asks a content negotiator class for a language. If the supported languages array is not empty, it will go for the most qualified one in that array.
     */
    public function getLang() {
        $ln = new LanguageNegotiator();
        //the language negotiator will always have at least one result, so we can pop the first one without any problem
        $language = $ln->pop();
        while ($ln->hasNext() && (sizeof($this->supportedLanguages()) == 0 || !in_array($language, $this->supportedLanguages()))) {
            $language = $ln->pop();
        }
        if (sizeof($this->supportedLanguages()) != 0 && !in_array($language, $this->supportedLanguages())) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Language: $language is not supported."), $exception_config);
        }
        return $language;
    }

    /**
     * setLinkHeader sets a Link header with next, previous
     * @param int $limit  The limitation of the amount of objects to return
     * @param int $offset The offset from where to begin to return objects (default = 0)
     */
    protected function setLinkHeader($page,$page_size,$referral = "next"){

        /**
         * Process the correct referral options(next | previous)
         */
        if($referral != "next" || $referral != "previous"){
             $log = new Logger('AReader');
             $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ERROR));
             $log->addError("No correct referral has been found, options are 'next' or 'previous', the referral given was: $referral");
        }

        header("Link: ". Config::get("general","hostname") . Config::get("general","subdir") . $this->package . "/" . $this->resource . ".about?page=" 
            . $page . "&page_size=" . $page_size . ";rel=" . $referral);

    }

}

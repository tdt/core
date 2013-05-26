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

    public static $BASICPARAMS = array("callback", "filterBy", "filterValue", "filterOp","page_size","page", "limit", "offset");

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
        if($referral != "next" && $referral != "previous"){
           $log = new Logger('AResourceStrategy');
           $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ERROR));
           $log->addError("No correct referral has been found, options are 'next' or 'previous', the referral given was: $referral");
       }

        /**
         * Check if the Link header has already been set, with a next relationship for example.
         * If so we have to append the Link header instead of hard setting it
         */
        $link_header_set = false;
        foreach(headers_list() as $header){
            if(substr($header,0,4) == "Link"){
                $header.=", ". Config::get("general","hostname") . Config::get("general","subdir") . $this->package . "/" . $this->resource . ".about?page="
                . $page . "&page_size=" . $page_size . ";rel=" . $referral;
                header($header);
                $link_header_set = true;
            }
        }

        if(!$link_header_set){
            header("Link: ". Config::get("general","hostname") . Config::get("general","subdir") . $this->package . "/" . $this->resource . ".about?page="
                . $page . "&page_size=" . $page_size . ";rel=" . $referral);
        }
    }

    /**
     * Calculate the limit and offset based on the request string parameters.
     */
    protected function calculateLimitAndOffset(){

        if(empty($this->limit) && empty($this->offset)){

            if(empty($this->page)){
                $this->page = 1;
            }

            if(empty($this->page_size)){
                $this->page_size = AResourceStrategy::$DEFAULT_PAGE_SIZE;
            }

            if($this->page == -1){ // Return all of the result-set == no paging.
                $this->limit = 2147483647; // max int on 32-bit machines
                $this->offset= 0;
                $this->page_size = 2147483647;
                $this->page = 1;
            }else{
                $this->offset = ($this->page -1)*$this->page_size;
                $this->limit = $this->page_size;
            }



        }else{

            if(empty($this->limit)){
                $this->limit = AResourceStrategy::$DEFAULT_PAGE_SIZE;
            }

            if(empty($this->offset)){
                $this->offset = 0;
            }

            if($this->limit == -1){
                $this->limit = 2147483647;
                $this->page = 1;
                $this->page_size = 2147483647;
                $this->offset = 0;
            }else{
                // calculate the page and size from limit and offset as good as possible
                // meaning that if offset<limit, indicates a non equal division of pages
                // it will try to restore that equal division of paging
                // i.e. offset = 2, limit = 20 -> indicates that page 1 exists of 2 rows, page 2 of 20 rows, page 3 min. 20 rows.
                // paging should be (x=size) x, x, x, y < x EOF
                $page = $this->offset/$this->limit;
                $page = round($page,0,PHP_ROUND_HALF_DOWN);
                if($page==0){
                    $page = 1;
                }
                $this->page = $page;
                $this->page_size = $this->limit ;
            }
        }
    }

}

<?php

/**
 * This is a class which will return all the available resources along with the documentation of that resource in this DataTank
 *
 * IMPORTANT NOTE!!!
 *
 * Make sure to define your logging format used in your Apace configuration!!
 * common:
 *
 * 127.0.0.1 - frank [10/Oct/2000:13:55:36 -0700] "GET /apache_pb.gif HTTP/1.0" 200 2326
 *
 * combined:
 *
 * 127.0.0.1 - frank [10/Oct/2000:13:55:36 -0700] "GET /apache_pb.gif HTTP/1.0" 200 2326 "http://www.example.com/start.html" "Mozilla/4.08 [en] (Win98; I ;Nav)"
 *
 *
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

namespace tdt\core\model\packages\core\TDTInfo;

use tdt\core\model\resources\read\AReader;
use tdt\core\model\ResourcesModel;
use tdt\core\utility\Config;
use tdt\core\utility\ApacheLogParser;
use tdt\cache\Cache;

class TDTInfoStatistics extends AReader {

    private $resourcestring = "TDTAdmin/Resources";
    private $date = "";

    public function __construct() {

    }

    public static function getParameters() {
        return array( "resourcestring" => "A full packageresource string identifying a resource of which you want to get some statistics from, by default TDTAdmin/Resources.",
            "date" => "The date to start the timeframe from, by default it's filled in with the current date. Use the format used by apache access log which is day in 2 digits, month in 3 letters, year in 4 digits i.e. 01/Feb/2008."    );
    }

    public static function getRequiredParameters() {
        return array();
    }

    public function setParameter( $key, $val ) {
        $this->$key = $val;
        if ( $key == "date" ) {
            $this->date = $val;
        }
    }

    /*
     * Read the access log and parse the correct lines from it.
     * Queries done for the passed days can be cached however! So
     * lets check the cache first and then do the parsing.
     */
    public function read() {

        if ( $this->date == "" ) {

            /*
             * Set the default date to today in the correct format.
             */
            $timezone = Config::get( "general", "timezone" );
            date_default_timezone_set( $timezone );
            $this->date = date( 'd/M/Y', time() );
        }

        if($this->checkCache()){
            return $this->checkCache();
        }else{

            $array_of_days = array();

            $apache_accesslog = Config::get( "general", "accesslogapache" );
            $apache_logformat = Config::get( "general", "apachelogformat" );
            $log_parser = new ApacheLogParser( $apache_accesslog, $apache_logformat );

            /*
             * Get the data per day from the given date.
             * It has been foreseen in the code ( see for loop ) that
             * multiple sequential dates can be asked for. However for ease of use
             * and performance reasons, we're currently return the stats for just 1 day. ( timeframe = 1)
             */
            $timeframe = 1;

            for ( $i=0; $i<$timeframe;$i++ ) {

                /*
                 * Prepare day
                 */

                $date = \DateTime::createFromFormat( "d/M/Y", $this->date );
                $interval = 'P'.$i.'D';
                $date->add( new \DateInterval( $interval ) );
                $day = $date->format( 'd/M/Y' );

                /*
                 * Prepare the filters to apply
                 */
                $filters = array( 'path' => array( 'regex' => "#.*$this->resourcestring.*#" ), 'date' => array( 'regex' => '#'.$day.'#' ) );
                $array_of_days[$day] = $this->initializeDayObject();

                /*
                 * Set the filters of the log parser
                 */
                $log_parser->filters = $filters;

                /*
                 * Get the data for the given timeframe
                 * Take into account the default limit of 50! and if
                 * there are more get them as well
                 */
                $result_set = $log_parser->getData();

                $day_fully_parsed = FALSE;
                $amount_of_hits_parsed = 0;

                while ( !$day_fully_parsed && count( $result_set->lines ) > 0 ) {

                    $amount_of_hits_parsed+= count( $result_set->lines );

                    /*
                     * Parse the data into array_of_days
                     */
                    foreach ( $result_set->lines as $line ) {

                        /*
                         * Count Request header
                         */
                        $method = $line["method"];
                        if ( isset( $array_of_days[$day]->$method ) ) {
                            $array_of_days[$day]->$method++;
                        }

                        /*
                         * Add the path as a hit and put count to 1
                         * If it exists already just increment the hit
                         */
                        $path = $line["path"];
                        if ( isset( $array_of_days[$day]->hits[$path] ) ) {
                            $array_of_days[$day]->hits[$path]->$method++;
                        }else {
                            $array_of_days[$day]->hits[$path]->$method = 1;
                        }

                    }

                    /*
                     * Check if more hits are to be parsed!
                     */
                    if ( $result_set->hits > $amount_of_hits_parsed ) {
                        $result_set = $log_parser->getData( ApacheLogParser::$DEFAULT_LIMIT, $amount_of_hits_parsed );
                    }else {
                        $day_fully_parsed = true;
                    }

                }
            }
            $c = Cache::getInstance($this->prepareCacheConfig());
            $c->set($this->buildCacheString(),$array_of_days, 604800); // cache it for a week.
            return $array_of_days;
    }


}

    /**
     * Builds a cache string based on the date and resourcestring
     *
     * @return string String that represents the date and resourcestring
     */
    private function buildCacheString() {
        return "STATISTICS__" .$this->resourcestring . "__" . $this->date;
    }

    /**
     * checkCache checks if the query has been set in the cache
     * It will return FALSE when nothing is found OR when the date is 'today'
     * because the access log can be changed during the current date
     * so we prefer not to cache it, in order to return the correct result.
     * @return cached_query_result|FALSE
     */
    private function checkCache() {

        if($this->date == date( 'd/M/Y', time())){
            return FALSE;
        }

        $c = Cache::getInstance($this->prepareCacheConfig());
        $cached_query = $c->get($this->buildCacheString());
        if(is_null($cached_query)){
            return FALSE;
        }else{
            return $cached_query;
        }

       /*
        $doc = $c->get($this->hostname . $this->subdir . "documentation");
        if (is_null($doc)) {
            $doc = new \stdClass();
            foreach ($factories as $factory) {
                $factory->makeDoc($doc);
            }
            $c->set($this->hostname . $this->subdir . "documentation", $doc, 60 * 60 * 60); // cache it for 1 hour by default
        */

        }

        private function initializeDayObject() {
            $day = new \stdClass();
            $day->GET = 0;
            $day->PUT = 0;
            $day->DELETE = 0;
            $day->hits = array();
            return $day;
        }

        public static function getDoc() {
            return "This resource performs some statistical functionality on the apache access.log file. It will return statistical data in a weekly timeframe starting from the date you enter.";
        }

    /*
     * prepare the caching configuration
     */

    private function prepareCacheConfig() {
        $cache_config = array();

        $cache_config["system"] = Config::get("general", "cache", "system");
        $cache_config["host"] = Config::get("general", "cache", "host");
        $cache_config["port"] = Config::get("general", "cache", "port");

        return $cache_config;
    }

}

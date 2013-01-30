<?php

/**
 * Class for reading(fetching) a generic resource
 *
 * @package The-Datatank/model/resources/read
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\resources\read;

use tdt\core\model\DBQueries;
use tdt\core\model\resources\GenericResource;

class GenericResourceReader extends AReader {

    private $genres;

    public function __construct($package, $resource, $RESTparameters) {
        parent::__construct($package, $resource, $RESTparameters);
        $this->genres = new GenericResource($this->package, $this->resource);
        $strategy = $this->genres->getStrategy();
        $this->parameters = array_merge($this->parameters, $strategy->documentReadParameters());
    }

    /**
     * read method
     */
    public function read() {       
        return $this->genres->read();
    }

    /**
     * get the documentation about getting of a resource
     */
    public function getReadDocumentation() {
        $result = DBQueries::getGenericResourceDoc($this->package, $this->resource);
        return isset($result["doc"]) ? $result["doc"] : "";
    }

    /**
     * A generic resource can't have parameters (yet), strategies can however
     */
    public function setParameter($key, $value) {
        
        /**
         * pass along the parameters to the strategy
         */
        $strategy = $this->genres->getStrategy();
        $strategy->setParameter($key, $value);
        
    }

}

?>
<?php

/**
 * This class handles a SPARQL query
 *
 * @copyright (C) 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 * @author Pieter Colpaert
 */

namespace tdt\core\strategies;

use tdt\core\model\resources\AResourceStrategy;
use tdt\framework\TDTException;
use RedBean_Facade as R;

class SPARQL extends AResourceStrategy {


    public function read(&$configObject,$package,$resource){
        $uri = $configObject->endpoint . '?query=' . urlencode($configObject->query) . '&format=' . urlencode("application/json");
        $data = \tdt\framework\Request::http($uri);
        return json_decode($data->data);
    }
    
    public function isValid($package_id,$generic_resource_id){
        $uri = $this->endpoint . '?query=' . urlencode($this->query) . '&format=' . urlencode("application/json");
        $data = \tdt\framework\Request::http($uri);
        $result = json_decode($data->data);
        if(!$result){
            throw new TDTException(500,array("Could not transform the json data from ". $uri ." to a php object model, please check if the json is valid."));
        }
        return true;
    }
    

    /**
     * The parameters returned are required to make this strategy work.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateRequiredParameters() {
        return array("endpoint", "query");
    }
    
    public function documentReadRequiredParameters(){
        return array();
    }
    

    public function documentUpdateRequiredParameters(){
        return array();
    }
    

   public function documentCreateParameters(){
        return array(
            "endpoint" => "The URI of the SPARQL endpoint.",
            "query" => "The SPARQL query"
        );
   }
   
   public function documentReadParameters(){
       return array();
   }
   
   public function documentUpdateParameters(){
       return array();
   }

   public function getFields($package,$resource){
       return array();
   }
    

}
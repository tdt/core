<?php
/**
 * An abstract class for JSON data
 *
 * @package The-Datatank/model/resources/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\strategies;

use tdt\core\model\resources\AResourceStrategy;
use tdt\framework\Log;
use tdt\framework\TDTException;

class JSON extends AResourceStrategy{
    
    public function read(&$configObject,$package,$resource){ 
        $data = \tdt\framework\Request::http($configObject->uri);
        return json_decode($data->data);
    }

    public function isValid($package_id,$generic_resource_id){
        $data = \tdt\framework\Request::http($this->uri);
        $result = json_decode($data->data);
        if(!$result){
            throw new TDTException(500,array("Could not transfrom the json data from ". $this->uri ." to a php object model, please check if the json is valid."));
        }
        return true;
    }

    public function documentCreateRequiredParameters(){
        return array("uri");
    }
    
    public function documentReadRequiredParameters(){
        return array();
    }
    

    public function documentUpdateRequiredParameters(){
        return array();
    }
    

   public function documentCreateParameters(){
       return array(
           "uri" => "The uri to the json document."
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
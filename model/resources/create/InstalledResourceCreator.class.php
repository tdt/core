<?php
/**
 * This class creates an installed resource entry in the back-end. 
 * The physical location is somewhere in custom/packages, but in order to "release" it you must map it onto a URL
 * to which the PUT request is made.
 *
 * @package The-Datatank/model/resources/create
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\resources\create;

use tdt\core\model\DBQueries;
use tdt\framework\TDTException;

class InstalledResourceCreator extends ACreator{

    public function __construct($package, $resource, $RESTparameters){
        parent::__construct($package, $resource, $RESTparameters);
    }

    /**
     * This overrides the previous defined required parameters by ACreator. It needs $strategy to be an instance of a strategy. Therefor setParameter needs to have been called upon with a generic_type as argument.
     */
    public function documentParameters(){
        $parameters = parent::documentParameters();
        $parameters["location"] = "The location, relative to the custom/packages folder, of your class file that represents an installed resource i.e. mypackage/myinstalledresource.class.php.";
        $parameters["classname"] = "The name of the class i.e. myinstalledresource.";
        return $parameters;
    }

    /**
     * This overrides the previous defined required parameters by ACreator. It needs $strategy to be an instance of a strategy. Therefor setParameter needs to have been called upon with a generic_type as argument.
     */
    public function documentRequiredParameters(){
        $parameters = parent::documentRequiredParameters();
        $parameters[]= "location";
        $parameters[] = "classname";
        return $parameters;
    }

    public function setParameter($key,$value){
        // set the correct parameters, to the this class or the strategy we're sure that every key,value passed is correct
        $this->$key = $value;
    }

    /**
     * execution method
     * Preconditions: 
     * parameters have already been set.
     */
    public function create(){
        /*
         * Create the package and resource entities and create a generic resource entry.
         * Then pick the correct strategy, and pass along the parameters!
         */
        // check if the location is legit
        
        if(file_exists("custom/packages/".$this->location )){
            include_once("custom/packages/".$this->location );
            if(class_exists($this->classname)){
                $package_id  = parent::makePackage($this->package);
                $resource_id = parent::makeResource($package_id, $this->resource, "installed");
                
                $meta_data_id = DBQueries::storeMetaData($resource_id,$this,array_keys(parent::documentMetaDataParameters()));
                DBQueries::storeInstalledResource($resource_id,$this->location,$this->classname);       
            }else{
                throw new TDTException(452,array("The classname $this->classname doesn't exist on location cores/core/custom/packages/$this->location"));
            }
        }else{
             throw new TDTException(452,array("The classname $this->classname doesn't exist on location cores/core/custom/packages/$this->location"));
        }
    }  
}
?>
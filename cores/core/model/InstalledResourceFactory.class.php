<?php
/**
 * This class will handle all resources installed in de package directory
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan a t iRail.be>
 */

class InstalledResourceFactory extends AResourceFactory{
    
    public function __construct() {
        AutoInclude::register("InstalledResourceCreator","cores/core/model/resources/create/InstalledResourceCreator.class.php");
        AutoInclude::register("InstalledResourceDeleter", "cores/core/model/resources/delete/InstalledResourceDeleter.class.php");
    }
    
    public function createCreator($package,$resource, $parameters, $RESTparameters){
        $creator = new InstalledResourceCreator($package,$resource, $RESTparameters);
        foreach($parameters as $key => $value){
            $creator->setParameter($key,$value);
        }
        return $creator;
    }
    
    public function createReader($package,$resource, $parameters, $RESTparameters){
        
        // location contains the full name of the file, including the .class.php extension
        $location = $this->getLocationOfResource($package,$resource);
        
        if(file_exists(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/packages/" . $location)){
            include_once(Config::get("general", "homedir") . Config::get("general","subdir") . "cores/core/custom/packages/" . $location);
            $classname = $this->getClassnameOfResource($package,$resource);
            $reader = new $classname($package,$resource, $RESTparameters);
            $reader->processParameters($parameters);
            return $reader;
        }else{
            throw new TDTException(404,array("cores/core/custom/packages/".$location));
        }
    }

    public function hasResource($package,$resource){
        $resource = DBQueries::hasInstalledResource($package, $resource);
        return isset($resource["present"]) && $resource["present"] >= 1;   
    }
    

    public function createDeleter($package,$resource, $RESTparameters){        
        $deleter = new InstalledResourceDeleter($package,$resource, $RESTparameters);
        return $deleter;
    }

    public function makeDoc($doc){
        //ask every resource we have for documentation

        foreach($this->getAllResourceNames() as $package => $resourcenames){
            if(!isset($doc->$package)){
                $doc->$package = new StdClass();
            }

            foreach($resourcenames as $resourcename){
                
                $example_uri = DBQueries::getExampleUri($package,$resourcename);
                $location = $this->getLocationOfResource($package,$resourcename);
                
                // file can always have been removed after adding it as a published resource
                if(file_exists(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/packages/".$location )){
                    $classname = $this->getClassnameOfResource($package,$resourcename);
                    $doc->$package->$resourcename = new StdClass();
                    include_once(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/packages/" . $location);
                    $doc->$package->$resourcename->doc = $classname::getDoc();
                    $doc->$package->$resourcename->requiredparameters = $classname::getRequiredParameters();
                    $doc->$package->$resourcename->parameters = $classname::getParameters();   
                    $doc->$package->$resourcename->example_uri = $example_uri;
                }
            }
        }
        return $doc;
    }

    public function makeDescriptionDoc($doc){
        //ask every resource we have for documentation
        
        foreach($this->getAllResourceNames() as $package => $resourcenames){
            if(!isset($doc->$package)){                
                $doc->$package = new StdClass();
                
            }

            foreach($resourcenames as $resourcename){
                
                $example_uri = DBQueries::getExampleUri($package,$resourcename);
                $location = $this->getLocationOfResource($package,$resourcename);
                
                // file can always have been removed after adding it as a published resource
                if(file_exists(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/packages/".$location )){
                    
                    $classname = $this->getClassnameOfResource($package,$resourcename);
                    $doc->$package->$resourcename = new StdClass();
                    include_once(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/packages/" . $location);
                    $doc->$package->$resourcename->doc = $classname::getDoc();
                    $doc->$package->$resourcename->requiredparameters = $classname::getRequiredParameters();
                    $doc->$package->$resourcename->parameters = $classname::getParameters();   
                    $doc->$package->$resourcename->example_uri = $example_uri;
                    $doc->$package->$resourcename->resource_type = "installed";
                    $doc->$package->$resourcename->location = $location;
                    $doc->$package->$resourcename->classname = $classname;
                }
            }
        }
        return $doc;
    }

    private function getCreationTime($package, $resource) {
        //if the object read is a directory and the configuration methods file exists, 
        //then add it to the installed packages
        $location = $this->getLocationofResource($package,$resource);
        if (file_exists(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/packages/" . $location )) {
            return filemtime(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/packages/" . $location );
        }
        return 0;
    }
    
    private function getModificationTime($package, $resource) {
        // for an existing folder you can only get the last modification date in php, so 
        return $this->getCreationTime($package, $resource);
    }

    protected function getAllResourceNames(){
        /**
         * Get all the physical locations of published installed resources
         */
        $resources = array();
        $installedResources = DBQueries::getAllInstalledResources();
        foreach($installedResources as $installedResource){
            if(!array_key_exists($installedResource["package"],$resources)){
                $resources[$installedResource["package"]] = array();
            }
            $resources[$installedResource["package"]][] = $installedResource["resource"];
        }
        return $resources;
    }

    private function getLocationOfResource($package,$resource){
        return DBQueries::getLocationofResource($package,$resource);
    }

    private function getClassnameOfResource($package,$resource){
        return DBQueries::getClassnameOfResource($package,$resource);
    }
    

    /**
     * Put together the deletion documentation for installed resources
     */
    public function makeDeleteDoc($doc){
        $d = new StdClass();
        $d->doc = "Installed resources can be deleted from its location, yet it's physical classfile will remain in the folderstructure of the custom/packages folder.";
        if(!isset($doc->delete)){
            $doc->delete = new StdClass();
        }
        $doc->delete->installed = new StdClass();
        $doc->delete->installed = $d;
    }

    /**
     * Put together the creation documentation for installed resources
     */
    public function makeCreateDoc($doc){

        $d = new StdClass();
        $installedResource = new InstalledResourceCreator("","",array());
        $d->doc = "You can PUT an installed resource when you have created a resource-class in the custom/packages folder.";
        $d->parameters = $installedResource->documentParameters();
        $d->requiredparameters = $installedResource->documentRequiredParameters();

        if(!isset($doc->create)){
            $doc->create = new stdClass();
        }
        $doc->create->installed = new stdClass();
        $doc->create->installed = $d;
    }
}

?>

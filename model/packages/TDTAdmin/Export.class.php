<?php
/**
 * This class will handle the export of resources
 *
 * @package The-Datatank/model/packages/TDTAdmin
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace TDTAdmin;

class TDTAdminExport extends AReader{
    

    private $descriptionDoc;

    public static function getParameters(){
        return array("export_package" => "The package name of which all or one resource(s) (depending on whether or not a resource parameters is passed as well) will be exported.",
                     "export_resource" => "The resource to be exported, be sure to pass along a package to identify the resource."
        );
    }

    public static function getRequiredParameters(){
	return array();
    }

    public function setParameter($key,$val){
        $this->$key = $val;
    }

    public function read(){
        $model = ResourcesModel::getInstance();


        /**
         * Check if package resource pair ( if any given ) are valid
         */
        $resources = array();
        $allDoc = $model->getAllDoc();
        $this->descriptionDoc = $model->getAllDescriptionDoc();
        $creationDoc = $model->getAllAdminDoc();
        
        /**
         * Different scenario's:
         * no package given
         * only package given -> exists ?
         *    yes : get all of the resources, exclude installed or core
         *    no  : throw exception
         * package and resource given -> existing pair ?
         *    yes : get the definition, exclude if installed or core
         *    no  : throw exception
         */
        
        if(!isset($this->export_package)){
            /**
             * fetch ALL the packages and ALL the resources (generic and remote ones)
             */
            $hash = (array)$allDoc;
            foreach($hash as $package => $resource){
                $hash = (array)$allDoc;
                $resource= (array)$hash[$package];
                $resource = array_keys($resource);
                $resources[$package] = array();
                foreach($resource as $resourceName){
                    if($this->isResourceExportable($package,$resourceName)){
                        $resourceObject = $this->createResourceObject($package,$resourceName);
                        array_push($resources[$package],$resourceObject);
                    }   
                }
            }
        }else if(isset($this->export_package) && !isset($this->export_resource)){
            $package = $this->export_package;
            if($model->hasPackage($this->export_package)){
                $hash = (array)$allDoc;
                $resource= (array)$hash[$package];
                $resource = array_keys($resource);
                $resources[$package] = array();
                foreach($resource as $resourceName){
                    if($this->isResourceExportable($package,$resourceName)){
                        $resourceObject = $this->createResourceObject($package,$resourceName);
                        array_push($resources[$package],$resourceObject);
                    }   
                }
            }else{
                throw new TDTException(452,array($this->export_package ." not found"));
            }
        }else{
            if($model->hasResource($this->export_package, $this->export_resource)){
                $resources[$this->export_package] = array();
                if($this->isResourceExportable($this->export_package, $this->export_resource)){
                    $resourceObject = $this->createResourceObject($this->export_package, $this->export_resource);
                    array_push($resources[$this->export_package],$resourceObject);
                }
            }else{
                throw new TDTException(452,array($this->export_package . "/" . $this->export_resource . " not found."));
            }
        }

        $resourceDumps = array();
        /**
         * For every resource:
         * resourceObjectArray is the array with all of the resourceObjects created in the above if-else structure
         * resourceDumps has a similar structure like resourceObjectArray, but now with Dumps (string) instead of a 
         * resourceObject
         */
         $resourceDumps = array();
        foreach($resources as $package => $resourceObjectArray){
            foreach($resourceObjectArray as $resourceObject){   
                /**
                 * Fetch all of the create parameters of the resource, according to the resource_type ( generic_type )
                 */
                $resource_type = $resourceObject->resource_type;
                $generic_type = "";
                if($resource_type == "generic"){
                    $generic_type = $resourceObject->generic_type;
                }
                
                $creationParameters = $creationDoc->create->$resource_type;
                if($resource_type == "generic"){
                    $creationParameters = $creationParameters[$generic_type];
                }
                
                $creationParameters = (array)$creationParameters;
                
                /**
                 * Fetch the properties of the resource
                 */
                $resourceDefinition = (array)$resourceObject->description;

                /**
                 * Only maintain the properties of the create-section
                 */
                foreach($creationParameters as $key => $value){
                    if(!isset($resourceDefinition[$key]) || $resourceDefinition[$key] != ""){
                        unset($resourceDefinition[$key]);
                    }
                }
                // make sure the string comes out well in the response strings contain t's and n's
                header("Content-Type: text/plain");
                $dump = $this->createDump($package,$resourceObject->resourcename,$resourceDefinition);
                array_push($resourceDumps, $dump);
                /**
                 * end for
                 */ 
            }
        }
        foreach($resourceDumps as $resourceDump){
            echo $resourceDump;
        }
        // prevent the formatter from formatting anything, because we just return flat raw php code!
        exit();
    }

    public static function getDoc(){
	return "This resource will export resource definitions to a PHP file. This PHP file can be used to add the exported resources.";
    }

    /**
     * Only remote, generic and installed resources should be exportable
     */
    private function isResourceExportable($package,$resource){
        return isset($this->descriptionDoc->$package->$resource->resource_type) && 
            ($this->descriptionDoc->$package->$resource->resource_type == "generic" || 
             $this->descriptionDoc->$package->$resource->resource_type == "remote"  ||
             $this->descriptionDoc->$package->$resource->resource_type == "installed");
    }

    /**
     * Create a resourceObject containing resourcename, resource_type ( and generic_type if resource_type = generic )
     * and entire description
     */
    private function createResourceObject($package,$resource){
        $resourceObject = new StdClass();
        $resourceObject->resourcename = $resource;
        $resourceObject->resource_type = $this->descriptionDoc->$package->$resource->resource_type;
        // also dump the entire body of the resource object description in it
        $resourceObject->description = $this->descriptionDoc->$package->$resource;
        // format the resource_type if it's generic
        if($resourceObject->resource_type == "generic"){
            $generic_type = $this->descriptionDoc->$package->$resource->generic_type;
            $resourceObject->generic_type = $generic_type;
        }
        return $resourceObject;
    }

    /**
     * Echo what should be in the php export file
     */
    private function createDump($package,$resource,$resourceDefinition){
        $dump ="";
        $dump.='<?php
    $url = "'. Config::get("general","hostname") . Config::get("general","subdir") . "TDTAdmin/Resources/".$package."/".$resource .'";
    $ch = curl_init();     
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);  
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_USERPWD,"'. Config::get("auth","api_user") . ':' . Config::get("auth","api_passwd") .'");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    $data = array( ';
         
        $count = 0;
        foreach($resourceDefinition as $key => $value){
            $count++;
            if(!is_array($value)){
                $dump.= '"'.$key.'"' . ' => "' . $value .'"';
            }else{
                $dump.= '"'.$key.'"' . " => array(";
                // array index, meant to know when to place a trailing "," or not
                $counter=0;
                foreach($value as $index => $mapping){
                    $counter++;
                    $dump.= '"'.$index .'"'. ' => "' . $mapping.'"';
                    if(count($value) != $counter){
                        $dump.= ",";
                    }
                }
                $dump.= ')';
            }
            if($count != count($resourceDefinition)){
                $dump.= ",t";
            }
        }
        
         
        $dump.= ');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $result = curl_exec($ch);  
    $responseHeader = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	 echo "The addition of the resource definition ". $url . " has ";	
	 if(strlen(strstr($responseHeader,"200"))>0){
    	echo "succeeded!n";
    }else{
    	echo "failed!n";
    }
	 echo $result;
    echo "n ============================================= n";
    curl_close($ch);  
?>';

        $dump.= "n";
        return $dump;
    }
}
?>

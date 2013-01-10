<?php
/**
 * An abstract class for tabular data
 *
 * @package The-Datatank/model/resources/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 */

namespace tdt\core\strategies;

use tdt\core\model\DBQueries;
use tdt\core\model\resources\AResourceStrategy;
use tdt\core\model\resources\GenericResource;
use tdt\framework\TDTException;

abstract class ATabularData extends  AResourceStrategy{

    protected $parameters = array(); // create parameters
    protected $updateParameters = array(); // update parameters

    function __construct(){                
        
        $this->parameters["columns"] = "An array that contains the name of the columns that are to be published, if an empty array is passed every column will be published. This array should be build as index => column_alias.";

        $this->parameters["column_aliases"] = "An array that contains the alias of a published column. This array should be build as column_name => column_alias. If no array is passed, the alias will be equal to the normal column name. If your column name,used as a key, contains whitespaces be sure to replace them with an underscore.";
        
        $this->parameters["limit"] = "The number of rows returned.";

    }

    /**
     * Mostly generic resources contain certain headers, or columns, in this function you can add
     * these columns to our published_columns table
     */
    protected function evaluateColumns($package_id,$generic_resource_id,$columns,$column_aliases,$PK){
        // check if PK is in the column keys
        if($PK != "" && !in_array($PK,array_keys($columns))){
            $this->throwException($package_id,$generic_resource_id,$PK ." 
                                  as a primary key is not one of the column name keys. 
                                  Either leave it empty or pass along the index of the column.");
        }
		
        foreach($columns as $index => $column){
            // replace whitespaces in columns by underscores
            if(!is_numeric($index)){
                $this->throwException($package_id,$generic_resource_id, "$index is not numeric!");
            }

            $column = trim($column);
            $formatted_column = preg_replace('/s+/','_',$column);

            if(!isset($column_aliases[$formatted_column])){
                $column_aliases[$formatted_column] = $formatted_column;
            }else{
                $column_aliases[$formatted_column] = trim($column_aliases[$formatted_column]);
                $formatted_column_alias = preg_replace('/s+/','_',$column_aliases[$formatted_column]);
                $column_aliases[$formatted_column] = $formatted_column_alias;
            }
			
            DBQueries::storePublishedColumn($generic_resource_id, $index,$formatted_column,$column_aliases[$formatted_column],
                                           ($PK != "" && $PK == $formatted_column?1:0));
        }
    }

    // fill in the configuration object that the strategy will receive
    public function read(&$configObject,$package,$resource){
         $published_columns = DBQueries::getPublishedColumns($configObject->gen_resource_id);
         $PK ="";
         $columns = array();
         $column_aliases = array();
         
         foreach ($published_columns as $result) {
             
             $columns[(string) $result["index"]] = $result["column_name"];
             $column_aliases[$result["column_name"]] = $result["column_name_alias"];

             if ($result["is_primary_key"] == 1) {
                 $PK = $column_aliases[$result["column_name"]];
             }
         }
         
         $configObject->columns = $columns;
         $configObject->column_aliases = $column_aliases;
         $configObject->PK = $PK;
    }
    

    /**
     * When a strategy is added, execute this piece of code.
     * It will generate a separate table in the back-end
     * specifically tuned for the parameters of the strategy.
     */
    public function onAdd($package_id, $gen_resource_id){
        if(!isset($this->PK)){
            $this->PK ="";
        }
        
        if($this->isValid($package_id,$gen_resource_id)){
            $this->evaluateColumns($package_id,$gen_resource_id,$this->columns,$this->column_aliases,$this->PK);
            // get the name of the class ( = strategyname)
            $strat = strtolower(get_class($this));
            $resource = R::dispense(GenericResource::$TABLE_PREAMBLE . $strat);
            $resource->gen_resource_id = $gen_resource_id;
            
            // for every parameter that has been passed for the creation of the strategy, make a datamember
            $createParams = array_keys($this->documentCreateParameters());

            foreach($createParams as $createParam){
                // dont add the columns parameter
                if($createParam != "columns" && $createParam != "column_aliases"){
                    if(!isset($this->$createParam)){
                        $resource->$createParam = "";
                    }else{
                        $resource->$createParam = $this->$createParam;
                    }   
                }
            }
            return R::store($resource);
        }else{
            /**
             * We cannot know what caused the invalidation of the resource, when a resource is invalid, the creator of
             * the strategy is expected to throw an exception of its own.
             */
            throw new TDTException(452,array("Something went wrong during the validation of the generic resource."));
        }
    }

   /**
     * This function gets the fields in a resource
     * @param string $package
     * @param string $resource
     * @return array Array with column names mapped onto their aliases
     */
    public function getFields($package, $resource) {
        
        $result = DBQueries::getGenericResourceId($package, $resource);
        $gen_res_id = $result["gen_resource_id"];

        $columns = array();

        // get the columns from the columns table
        $allowed_columns = DBQueries::getPublishedColumns($gen_res_id);

        /**
         * columns can have an alias, if not their alias is their own name
         */
        foreach ($allowed_columns as $result) {
            if ($result["column_name_alias"] != "") {
                $columns[(string) $result["column_name"]] = $result["column_name_alias"];
            } else {
                $columns[(string) $result["column_name"]] = $result["column_name"];
            }
        }
        
        return array_values($columns);
    }	
}
?>
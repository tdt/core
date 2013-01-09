<?php
/**
 * The abstract class for a factory: check documentation on the Factory Method Pattern if you don't understand this code.
 *
 * @package The-Datatank/resources
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 */

namespace tdt\core\model\resources;

class GenericResource{
    
    public static $TABLE_PREAMBLE = "generic_resource_";
    
    private $package;
    private $resource;
    private $strategyname;
    private $strategy;
    
    public function __construct($package,$resource){
        $this->package = $package;
        $this->resource = $resource;
        $result = tdt\core\model\DBQueries::getGenericResourceType($package, $resource);
        $this->strategyname = $result["type"];
    }

    /**
     * Gets the strategy of the generic resource.
     * @return $mixed Class instance of a strategy.
     */
    public function getStrategy(){
        if(is_null($this->strategy)){
            include_once("custom/strategies/" . $this->strategyname . ".class.php");
            $this->strategy = new $this->strategyname();
        }
        return $this->strategy;
    }    

    /**
     * Read a generic resource, by calling its strategy's read function
     * @return $mixed Class which holds the data from a certain datasource.
     */
    public function read(){
        $strat = $this->getStrategy();
        // ask for all of the parameters of the strategy
        $parameters = array_keys($strat->documentCreateParameters());

        // pass these parameters onto the createConfig to create the config object
        $configObject = $this->createConfigObject($parameters,$strat);

        // give the config object to the read function!
        return $strat->read($configObject,$this->package,$this->resource);
    }

    /**
     * Read a generic resource, but with passing a filter from the AST
     */
    public function readAndProcessQuery($query){

        $strat = $this->getStrategy();
        // ask for all of the parameters of the strategy
        $parameters = array_keys($strat->documentCreateParameters());

        // pass these parameters onto the createConfig to create the config object
        $configObject = $this->createConfigObject($parameters,$strat);

        // give the config object to the read function!
        // and pass along additional information
        $filterParameters = array();
        $filterParameters["configObject"] = $configObject;
        $filterParameters["package"] = $this->package;
        $filterParameters["resource"] = $this->resource;

        return $strat->readAndProcessQuery($query,$filterParameters);
    }
    

    /**
     * Get the generic resource info of the strategy.
     * input is an array of parameters
     */
    private function createConfigObject($parameters,$strat){
        $configObject = new stdClass();
        $columnstring = implode($parameters, ",");
        $resource_table = tdt\core\model\resources\GenericResource::$TABLE_PREAMBLE . strtolower(get_class($strat));
        $columnstring = $columnstring . ",gen_resource_id";
        $query = R::getRow(
            "SELECT *
             FROM  package,resource, generic_resource, $resource_table
             WHERE package.full_package_name=:package and resource.resource_name=:resource
                   and package.id=resource.package_id 
                   and resource.id = generic_resource.resource_id
                   and generic_resource.id= $resource_table.gen_resource_id",
            array(':package' => $this->package, ':resource' => $this->resource)
        );

        // attach every parameter to the config object
        foreach($parameters as $param){
            if(isset($query[$param])){
                $configObject->$param = $query[$param];    
            }
        }

        $configObject->gen_resource_id = $query["gen_resource_id"];
        return $configObject;
    }
}

?>
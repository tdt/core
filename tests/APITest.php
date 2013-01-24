<?php

/**
 * A class that provides end2end core API testing
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */
require "vendor/autoload.php";

use tdt\core\utility\Config;
use tdt\core\model\ResourcesModel;

class APITest extends \PHPUnit_Framework_TestCase {

    private $config;
    
    public function __construct() {
        /*
        * Prepare the configuration that will be used throughout the creation/reading/deleting
        * process of the test.  
        */
        $configArray = array("general" => array("hostname" => "", "subdir" => "", "defaultformat" => "json",
            "cache" => array("system" => "NoCache","host"=>"", "port"=>"")),
            "db" => array("system" => "mysql", "host"=>"localhost","user"=>"root", "password" => "", "name" => "myapp_test"),
            "logging" => array("enabled" => false, "path" => ""));

        Config::setConfig($configArray);


    }

    /*
     * Test function to check if a CSV file is added and removed correctly
     * and in between can be read as well in a correct way. 
     */
    public function testCSV() {                            

        $TEST_PACKAGE_NAME = "UNITTESTCSV";
        $TEST_RESOURCE_NAME = "csv1";

        $parameters = array(
            'documentation' => "This is a test case for unittesting a CSV resource.",
            'resource_type' => "generic/CSV",
            'delimiter' => ";",
            'uri' => __DIR__ . "/data/CSVData.csv"
        );    
        
        /*
         * Try creating a resource, if anything fails, the test fails
         */
        $create_resource = true;
        try{
            $model = ResourcesModel::getInstance();
            $model->createResource($TEST_PACKAGE_NAME . "/" . $TEST_RESOURCE_NAME,$parameters); 
        }catch(Exception $ex){            
            $create_resource = false;
        }
        
        $this->assertTrue($create_resource);
        
        /*
         * Try reading the datasource
         */
        $read_datasource = true;
        try{
            $model = $model = ResourcesModel::getInstance();
            $csv_object = $model->readResource($TEST_PACKAGE_NAME,$TEST_RESOURCE_NAME,array(),array());

            // Lets get the first rowobject and compare some values.
            // Clayton;Ap #630-7719 Scelerisque Road;ac.arcu@facilisismagna.ca;Pierre
            $object1 = array_shift($csv_object);
            $this->assertEquals("ac.arcu@facilisismagna.ca",$object1->email);    
            $this->assertEquals("Pierre",$object1->city); 
            $this->assertEquals("Clayton",$object1->Name);                   
            $this->assertEquals("Ap #630-7719 Scelerisque Road",$object1->Street_Address);
            
        }catch(Exception $ex){                   
            $read_datasource = false;
        }

        $this->assertTrue($read_datasource);

        /*
         * Try deleting the resource
         */
        $delete_datasource = true;
        try{
            $model = ResourcesModel::getInstance();
            $model->deleteResource($TEST_PACKAGE_NAME, $TEST_RESOURCE_NAME,array());
        }catch(Exception $ex){            
            $delete_datasource = false;
        }

        $this->assertTrue($delete_datasource);
    }

    public function testDB(){

        $TEST_PACKAGE_NAME = "UNITTESTDB";
        $TEST_RESOURCE_NAME = "db1";

        /*
         * Pass along the database parameters leading to the test data
         */
        $parameters = array(
            'documentation' => "This is a test case for unittesting a DB resource.",
            'resource_type' => "generic/DB",
            'db_table' => "test_table",
            'location' => "localhost",
            'username' => "root",
            'password' => "",
            'db_name' => "myapp_test",
            'db_type' => "MySQL"

        );    
        
        /*
         * Try creating a resource, if anything fails, the test fails
         */
        $create_resource = true;
        try{
            $model = ResourcesModel::getInstance();
            $model->createResource($TEST_PACKAGE_NAME . "/" . $TEST_RESOURCE_NAME,$parameters); 
        }catch(Exception $ex){           
            $create_resource = false;
        }
        
        $this->assertTrue($create_resource);
        
        /*
         * Try reading the datasource
         */
        $read_datasource = true;
        try{
            $model = $model = ResourcesModel::getInstance();
            $db_object = $model->readResource($TEST_PACKAGE_NAME,$TEST_RESOURCE_NAME,array(),array());

            // Lets get the first rowobject and compare some values.
            // Clayton;Ap #630-7719 Scelerisque Road;ac.arcu@facilisismagna.ca;Pierre
            $object1 = array_shift($db_object);
            $this->assertEquals("Yoshi",$object1->Name);
            $this->assertEquals("Weber",$object1->Surname);
            $this->assertEquals("Lakewood",$object1->City);
            $this->assertEquals("6435",$object1->Income);
            
        }catch(Exception $ex){   
            var_dump($e->getTrace());                 
            $read_datasource = false;
        }

        $this->assertTrue($read_datasource);

        /*
         * Try deleting the resource
         */
        $delete_datasource = true;
        try{
            $model = ResourcesModel::getInstance();
            $model->deleteResource($TEST_PACKAGE_NAME, $TEST_RESOURCE_NAME,array());
        }catch(Exception $ex){           
            $delete_datasource = false;
        }

        $this->assertTrue($delete_datasource);
    }

}
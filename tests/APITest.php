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
use RedBean_Facade as R;

class APITest extends \PHPUnit_Framework_TestCase {

    private $config;

    public function __construct() {

        parent::__construct('testCSV');
        /*
        * Prepare the configuration that will be used throughout the creation/reading/deleting
        * process of the test.
        */
        $configArray = array("general" => array("hostname" => "", "subdir" => "", "defaultformat" => "json",
            "cache" => array("system" => "NoCache","host"=>"", "port"=>"")),
        "db" => array("system" => "mysql", "host"=>"localhost","user"=>"root", "password" => "", "name" => "myapp_test"),
        "logging" => array("enabled" => false, "path" => "C:\wamp\www\startLogs"));

        Config::setConfig($configArray);


    }

    protected function setUp(){
        parent::setUp();

        ob_start(); // <-- very important!
    }

    protected function tearDown(){
        header_remove(); // <-- VERY important.
        parent::tearDown();
    }

    /*
     * Test function to check if a CSV strategy is working correctly.
     * Tests: Create,Read,Delete
     */
    /**
     * @runInSeparateProcess
     */
    public function testCSV() {

        $TEST_PACKAGE_NAME = "unittestcsv";
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
            echo $ex->getMessage();
            ob_flush();
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
            header_remove();
            // Lets get the first rowobject and compare some values.
            // Clayton;Ap #630-7719 Scelerisque Road;ac.arcu@facilisismagna.ca;Pierre
            $object1 = array_shift($csv_object);
            $this->assertEquals("ac.arcu@facilisismagna.ca",$object1->email);
            $this->assertEquals("Pierre",$object1->city);
            $this->assertEquals("Clayton",$object1->name);
            $this->assertEquals("Ap #630-7719 Scelerisque Road",$object1->street_address);

        }catch(Exception $ex){
            var_dump($ex->getMessage());
            die();
            ob_flush();
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
            echo $ex->getMessage();
            $delete_datasource = false;
        }

        $this->assertTrue($delete_datasource);
    }

    /*
     * Test function to check if the DB strategy is working correctly.
     * Tests: Create,Read,Delete
     */
    /**
     * @runInSeparateProcess
     */
    public function testDB(){


        $TEST_PACKAGE_NAME = "UNITTESTDB";
        $TEST_RESOURCE_NAME = "db1";

        $DB_HOST = "localhost";
        $DB_USER = "root";
        $DB_PASSWORD = "";
        $DB_NAME = "myapp_test";
        $DB_TABLE = "test_table";
        $DB_TYPE = "mysql";

        /*
         * Pass along the database parameters leading to the test data
         */
        $parameters = array(
            'documentation' => "This is a test case for unittesting a DB resource.",
            'resource_type' => "generic/DB",
            'db_table' => $DB_TABLE,
            'location' => $DB_HOST,
            'username' => $DB_USER,
            'password' => "",
            'db_name' => $DB_NAME,
            'db_type' => $DB_TYPE

            );

        /*
         * Try creating a resource, if anything fails, the test fails
         */
        $create_resource = true;
        try{

            /*
             * Add some data to the datatable first
             * using RedBean.
             */

            R::setup($DB_TYPE . ":host=" . $DB_HOST . ";dbname=" . $DB_NAME, $DB_USER, "");
            $person = R::dispense($DB_TABLE);
            $person->name = "Reginald";
            $person->surname = "TSM";
            $person->city = "San Francisco";
            $person->income = "9000";
            R::store($person);

            /*
             * Create the resource definition of the datatable
             */

            $model = ResourcesModel::getInstance();
            $model->createResource($TEST_PACKAGE_NAME . "/" . $TEST_RESOURCE_NAME,$parameters);

        }catch(Exception $ex){
            echo $ex->getMessage();
            $create_resource = false;
        }
        $this->assertTrue(false);
        $this->assertTrue($create_resource);

        /*
         * Try reading the datasource
         */

        $read_datasource = true;
        try{

            /*
             * Access the model to read the datasource
             */
            $model = $model = ResourcesModel::getInstance();
            $db_object = $model->readResource($TEST_PACKAGE_NAME,$TEST_RESOURCE_NAME,array(),array());

            // Lets get the object that we injected in the data table and check if it matches
            $object1 = array_shift($db_object);

            $this->assertEquals("Reginald",$object1->name);
            $this->assertEquals("TSM",$object1->surname);
            $this->assertEquals("San Francisco",$object1->city);
            $this->assertEquals("9000",$object1->income);

        }catch(Exception $ex){
            echo $ex->getMessage();
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
            echo $ex->getMessage();
            $delete_datasource = false;
        }

        $this->assertTrue($delete_datasource);
    }

    /*
     * Test function to check if the JSON strategy is working correctly
     * Tests: Create,Read,Delete
     */
    /**
     * @runInSeparateProcess
     */
    public function testJSON(){

        $TEST_PACKAGE_NAME = "UNITTESTJSON";
        $TEST_RESOURCE_NAME = "json1";

        $parameters = array(
            'documentation' => "This is a test case for unittesting a JSON resource.",
            'resource_type' => "generic/JSON",
            'uri' => "http://data.appsforghent.be/TDTInfo/Resources.json"
            );

        /*
         * Try creating a resource, if anything fails, the test fails
         */
        $create_resource = true;
        try{
            $model = ResourcesModel::getInstance();
            $model->createResource($TEST_PACKAGE_NAME . "/" . $TEST_RESOURCE_NAME,$parameters);
        }catch(Exception $ex){
            echo $ex->getMessage();
            $create_resource = false;
        }

        $this->assertTrue($create_resource);

        /*
         * Try reading the datasource
         */
        $read_datasource = true;
        try{
            $model = $model = ResourcesModel::getInstance();
            $json_object = $model->readResource($TEST_PACKAGE_NAME,$TEST_RESOURCE_NAME,array(),array());

            if(!is_object($json_object->Resources)){
                $read_datasource = false;
            }


        }catch(Exception $ex){
            echo $ex->getMessage();
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
            echo $ex->getMessage();
            $delete_datasource = false;
        }

        $this->assertTrue($delete_datasource);

    }

}
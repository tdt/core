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

class DBTest extends \PHPUnit_Framework_TestCase {

    private $config;

    public function __construct() {

        parent::__construct('testDB');
        /*
        * Prepare the configuration that will be used throughout the creation/reading/deleting
        * process of the test.
        */
        $configArray = array("general" => array("hostname" => "", "subdir" => "", "defaultformat" => "json",
            "cache" => array("system" => "NoCache","host"=>"", "port"=>"")),
        "db" => array("system" => "mysql", "host"=>"localhost","user"=>"root", "password" => "", "name" => "coretest"),
        "logging" => array("enabled" => false, "path" => ""));

        Config::setConfig($configArray);


    }

    protected function setUp(){
        parent::setUp();

        ob_start(); // <-- very important!
    }

    protected function tearDown(){
        header_remove(); // <-- very important.
        parent::tearDown();
    }
    /*
     * Test function to check if the DB strategy is working correctly. Note that we only a MySQL driver.
     * Tests: Create,Read,Delete
     */
    /**
     * @runInSeparateProcess
     */
    public function testDB(){

        $TEST_PACKAGE_NAME = "UNITTESTDB";
        $TEST_RESOURCE_NAME = "db";

        $DB_HOST = "localhost";
        $DB_USER = "root";
        $DB_PASSWORD = "";
        $DB_NAME = "coretest";
        $DB_TABLE = "person";
        $DB_TYPE = "mysql";
      
        $parameters = array(
            'documentation' => "This is a test case for unittesting a DB resource.",
            'resource_type' => "generic/DB",
            'db_table' => 'person',
            'location' => 'localhost',
            'username' => 'root',
            'password' => "",
            'db_name' => 'coretest',
            'db_type' => 'mysql'

            );
        //$this->expectOutputString('');
        
        /*
         * Try creating a resource, if anything fails, the test fails
         */
        $create_resource = true;
        try{
           
            $con = mysqli_connect("localhost","root","","coretest");
            // Check connection
            if (mysqli_connect_errno()){
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            // Create table
            $sql="CREATE TABLE person(name VARCHAR(30),surname VARCHAR(30),city VARCHAR(256), income INT)";

            // Execute query
            if (!mysqli_query($con,$sql)){
                echo "Error creating table: " . mysqli_error($con);
            }

            mysqli_query($con,"INSERT INTO person (name, surname, city, income) VALUES ('Reginald', 'Dinh','San Francisco', 9000)");
            mysqli_close($con);           

        
            // Create the resource definition
            $model = ResourcesModel::getInstance();
            $model->createResource($TEST_PACKAGE_NAME . "/" . $TEST_RESOURCE_NAME,$parameters);

        }catch(Exception $ex){
            echo $ex->getMessage();            
            $create_resource = false;
        }        
        $this->assertTrue($create_resource);

        // Read the datasource
        $read_datasource = true;
        try{
           
            $model = $model = ResourcesModel::getInstance();
            $db_object = $model->readResource($TEST_PACKAGE_NAME,$TEST_RESOURCE_NAME,array(),array());

            // Lets get the object that we injected in the data table and check if it matches
            $object1 = array_shift($db_object);
            
            $this->assertEquals("Reginald",$object1->name);
            $this->assertEquals("Dinh",$object1->surname);
            $this->assertEquals("San Francisco",$object1->city);
            $this->assertEquals(9000,$object1->income);

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
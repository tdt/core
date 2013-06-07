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

class CSVTest extends \PHPUnit_Framework_TestCase {

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
}
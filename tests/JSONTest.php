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

class JSONTest extends \PHPUnit_Framework_TestCase {

    private $config;

    public function __construct() {

        parent::__construct('testJSON');
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
     * Test function to check if the JSON strategy is working correctly
     * Tests: Create,Read,Delete
     */
    /**
     * @runInSeparateProcess
     */
    public function testJSON(){

        $TEST_PACKAGE_NAME = "UNITTESTJSON";
        $TEST_RESOURCE_NAME = "json";

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

<?php


use tdt\core\definitions\DefinitionController;
use tdt\core\datasets\DatasetController;
use Symfony\Component\HttpFoundation\Request;

class XmlTest extends TestCase{

    // This array holds the names of the files that can be used
    // to test the xml definitions.
    private $test_data = array(
                'persons',
            );

    public function test_put_api(){


        // Publish each xml file in the test xml data folder.
        foreach($this->test_data as $file){

            // Set the definition parameters.
            $data = array(
                'description' => "A xml publication from the $file xml file.",
                'delimiter' => ',',
                'uri' => __DIR__ . "/data/xml/$file.xml",
            );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.xml'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $response = DefinitionController::handle("xml/$file");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_get_api(){

        // Request the data for each of the test xml files.
        foreach($this->test_data as $file){

            $file = 'xml/'. $file .'.json';
            $this->updateRequest('GET');

            $response = DatasetController::handle($file);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_delete_api(){

        // Delete the published definition for each test xml file.
        foreach($this->test_data as $file){

            $this->updateRequest('DELETE');

            $response = DefinitionController::handle("xml/$file");
            $this->assertEquals(200, $response->getStatusCode());
        }
    }
}
<?php


use tdt\core\definitions\DefinitionController;
use tdt\core\datasets\DatasetController;
use Symfony\Component\HttpFoundation\Request;

class JsonTest extends TestCase{

    // This array holds the names of the files that can be used
    // to test the json definitions.
    private $test_data = array(
                'complex_persons',
                'simple_persons',
            );

    public function test_put_api(){

        // Publish each json file in the test json data folder.
        foreach($this->test_data as $file){

            // Set the definition parameters.
            $data = array(
                'description' => "A json publication from the $file json file.",
                'delimiter' => ',',
                'uri' => 'file://' . __DIR__ . "/data/json/$file.json",
                );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.json'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $response = DefinitionController::handle("json/$file");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_get_api(){

        // Request the data for each of the test json files.
        foreach($this->test_data as $file){

            $file = 'json/'. $file .'.json';
            $this->updateRequest('GET');

            $response = DatasetController::handle($file);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_delete_api(){

        // Delete the published definition for each test json file.
        foreach($this->test_data as $file){

            $this->updateRequest('DELETE');

            $response = DefinitionController::handle("json/$file");
            $this->assertEquals(200, $response->getStatusCode());
        }
    }
}
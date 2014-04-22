<?php

namespace Tdt\Core\Tests\Api;

use Tdt\Core\Tests\TestCase;
use Tdt\Core\Definitions\DefinitionController;
use Tdt\Core\Datasets\DatasetController;
use Symfony\Component\HttpFoundation\Request;

class JsonTest extends TestCase
{

    // This array holds the names of the files that can be used
    // to test the json definitions.
    private $test_data = array(
                'complex_persons',
                'simple_persons',
            );

    public function testPutApi()
    {

        // Publish each json file in the test json data folder.
        foreach ($this->test_data as $file) {

            // Set the definition parameters.
            $data = array(
                'description' => "A JSON publication from the $file json file.",
                'uri' => app_path() . "/storage/data/tests/json/$file.json",
                'type' => 'json'
                );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.definition+json'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle("json/$file");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testGetApi()
    {

        // Request the data for each of the test json files.
        foreach ($this->test_data as $file) {

            $file = 'json/'. $file .'.json';
            $this->updateRequest('GET');

            $controller = \App::make('Tdt\Core\Datasets\DatasetController');

            $response = $controller->handle($file);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testUpdateApi()
    {

        foreach ($this->test_data as $file) {

            $updated_description = 'An updated description for ' . $file;

            $identifier = 'json/' . $file;

            // Set the fields that we're going to update
            $data = array(
                'description' => 'An updated description',
            );

            // Set the correct headers
            $headers = array('Content-Type' => 'application/tdt.definition+json');

            $this->updateRequest('PATCH', $headers, $data);

            // Test the patch function on the definition controller
            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle($identifier);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testDeleteApi()
    {
        // Delete the published definition for each test json file.
        foreach ($this->test_data as $file) {

            $this->updateRequest('DELETE');

            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle("json/$file");
            $this->assertEquals(200, $response->getStatusCode());
        }

        // Check if everything is deleted properly.
        $definitions_count = \Definition::all()->count();
        $json_count = \JsonDefinition::all()->count();

        $this->assertTrue($json_count == 0);
        $this->assertTrue($definitions_count == 0);
    }
}

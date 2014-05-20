<?php

namespace Tdt\Core\Tests\Api;

use Tdt\Core\Tests\TestCase;
use Tdt\Core\Definitions\DefinitionController;
use Tdt\Core\Datasets\DatasetController;
use Symfony\Component\HttpFoundation\Request;
use \Definition;
use \JsonldDefinition;

class JsonldTest extends TestCase
{

    // This array holds the names of the files that can be used
    // to test the json definitions.
    private $test_data = array(
                array(
                    'type' => 'file',
                    'uri' => 'example1',
                    'cname' => 'example1',
                ),
                array(
                    'type' => 'uri',
                    'uri' => 'http://me.markus-lanthaler.com/',
                    'cname' => 'http example',
                ),
            );

    public function testPutApi()
    {
        // Publish each json file in the test json data folder.
        foreach ($this->test_data as $file_config) {

            $name = $file_config['cname'];

            $uri = '';

            if ($file_config['type'] == 'file') {

                $file = $file_config['uri'];
                $uri = app_path() . "/storage/data/tests/jsonld/$file.jsonld";

            } else {
                $uri = $file_config['uri'];
            }

            // Set the definition parameters.
            $data = array(
                'description' => "A JSON-LD publication from the $name jsonld file.",
                'uri' => $uri,
                'type' => 'jsonld'
            );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.definition+json'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle("jsonld/$name");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testGetApi()
    {
        // Request the data for each of the test json-ld files.
        foreach ($this->test_data as $file_config) {

            $name = $file_config['cname'];

            $file = 'jsonld/'. $name .'.jsonld';
            $this->updateRequest('GET');

            $controller = \App::make('Tdt\Core\Datasets\DatasetController');

            $response = $controller->handle($file);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testUpdateApi()
    {
        foreach ($this->test_data as $file_config) {

            $name = $file_config['cname'];

            $updated_description = 'An updated description for ' . $name;

            $identifier = 'jsonld/' . $name;

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
        foreach ($this->test_data as $file_config) {

            $name = $file_config['cname'];

            $this->updateRequest('DELETE');

            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle("jsonld/$name");
            $this->assertEquals(200, $response->getStatusCode());
        }

        // Check if everything is deleted properly.
        $definitions_count = Definition::all()->count();
        $jsonld_count = JsonldDefinition::all()->count();

        $this->assertEquals(0, $jsonld_count);
        $this->assertEquals(0, $definitions_count);
    }
}

<?php

namespace Tdt\Core\Tests\Api;

use Tdt\Core\Tests\TestCase;
use Tdt\Core\Definitions\DefinitionController;
use Tdt\Core\Datasets\DatasetController;
use Symfony\Component\HttpFoundation\Request;

class XlsTest extends TestCase
{

    // This array holds the names of the files and their correspondive
    // sheets that can be used to test the xls definitions.
    private $test_data = array(
                array(
                    'file' => 'tabular',
                    'extension' => 'xlsx',
                    'sheet' => 'Sheet1',
                ),
            );

    public function testPutApi()
    {

        // Publish each xls file in the test xls data folder.
        foreach ($this->test_data as $entry) {

            $file = $entry['file'];
            $extension = $entry['extension'];
            $sheet = $entry['sheet'];

            // Set the definition parameters.
            $data = array(
                'description' => "An xls publication from the $file xls file.",
                'sheet' => $sheet,
                'uri' => app_path() . "/storage/data/tests/xls/$file" . '.' . $extension,
                'type' => 'xls'
            );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.definition+json'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');
            $response = $controller->handle("xls/$file");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testGetApi()
    {

        // Request the data for each of the test xls files.
        foreach ($this->test_data as $entry) {

            $file = $entry['file'];
            $extension = $entry['extension'];
            $sheet = $entry['sheet'];

            $uri = 'xls/'. $file .'.json';
            $this->updateRequest('GET');

            $controller = \App::make('Tdt\Core\Datasets\DatasetController');

            $response = $controller->handle($uri);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testUpdateApi()
    {

        foreach ($this->test_data as $entry) {

            $name = $entry['file'];
            $updated_description = 'An updated description for ' . $name;
            $identifier = 'xls/' . $name;

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

        // Delete the published definition for each test xls file.
        foreach ($this->test_data as $entry) {

            $file = $entry['file'];

            $this->updateRequest('DELETE');

            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle("xls/$file");
            $this->assertEquals(200, $response->getStatusCode());
        }

        // Check if everything is deleted properly.
        $definitions_count = \Definition::all()->count();
        $xls_count = \XlsDefinition::all()->count();

        $this->assertTrue($xls_count == 0);
        $this->assertTrue($definitions_count == 0);
    }
}

<?php

namespace Tdt\Core\Tests\Api;

use Tdt\Core\Tests\TestCase;
use Tdt\Core\Definitions\DefinitionController;
use Tdt\Core\Datasets\DatasetController;
use Symfony\Component\HttpFoundation\Request;

class CsvTest extends TestCase
{

    // This array holds the names of the files that can be used
    // to test the csv definitions.
    private $test_data = array(
                'comma_in_quotes',
                'escaped_quotes',
                'json',
                'latin1',
                'newlines',
                'quotes_and_newlines',
                'simple',
                'utf8',
            );

    public function testPutApi()
    {

        // Publish each CSV file in the test csv data folder.
        foreach ($this->test_data as $file) {

            // Set the definition parameters.
            $data = array(
                'description' => "A CSV publication from the $file csv file.",
                'delimiter' => ',',
                'uri' => app_path() . "/storage/data/tests/csv/$file.csv",
                'type' => 'csv'
            );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.definition+json'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle("csv/$file");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testGetApi()
    {

        // Request the data for each of the test csv files.
        foreach ($this->test_data as $file) {

            $file = 'csv/'. $file .'.json';
            $this->updateRequest('GET');

            $controller = \App::make('Tdt\Core\Datasets\DatasetController');

            $response = $controller->handle($file);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function testUpdateApi()
    {

        foreach ($this->test_data as $file) {

            // Test the patch function on the definition controller
            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $this->updateRequest('GET');
            $definition = $controller->handle('csv/' . $file)->getOriginalContent();
            $definition = json_decode($definition, true);

            $previous_description = $definition['description'];

            $identifier = 'csv/' . $file;

            // Set the fields that we're going to update
            $data = array(
                'description' => 'An updated description',
            );

            // Set the correct headers
            $headers = array('Content-Type' => 'application/tdt.definition+json');

            $this->updateRequest('PATCH', $headers, $data);

            $response = $controller->handle($identifier);
            $this->assertEquals(200, $response->getStatusCode());

            $this->updateRequest('GET', array());

            $definition = $controller->handle('csv/' . $file)->getOriginalContent();
            $definition = json_decode($definition, true);

            $updated_description = $definition['description'];

            $this->assertTrue($updated_description != $previous_description);
        }
    }

    public function testDeleteApi()
    {
        // Delete the published definition for each test csv file.
        foreach ($this->test_data as $file) {

            $this->updateRequest('DELETE');

            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle("csv/$file");
            $this->assertEquals(200, $response->getStatusCode());
        }

        // Check if everything is deleted properly.
        $definitions_count = \Definition::all()->count();
        $csv_count = \CsvDefinition::all()->count();

        $this->assertTrue($csv_count == 0);
        $this->assertTrue($definitions_count == 0);
    }
}

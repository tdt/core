<?php


use tdt\core\definitions\DefinitionController;
use tdt\core\datasets\DatasetController;
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

    public function test_put_api()
    {

        // Publish each CSV file in the test csv data folder.
        foreach ($this->test_data as $file) {

            // Set the definition parameters.
            $data = array(
                'description' => "A CSV publication from the $file csv file.",
                'delimiter' => ',',
                'uri' => 'file://' . __DIR__ . "/data/csv/$file.csv",
                'type' => 'csv'
            );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.definition+json'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $controller = \App::make('tdt\core\definitions\DefinitionController');

            $response = $controller->handle("csv/$file");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_get_api()
    {

        // Request the data for each of the test csv files.
        foreach ($this->test_data as $file) {

            $file = 'csv/'. $file .'.json';
            $this->updateRequest('GET');

            $controller = \App::make('tdt\core\datasets\DatasetController');

            $response = $controller->handle($file);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_delete_api()
    {

        // Delete the published definition for each test csv file.
        foreach ($this->test_data as $file) {

            $this->updateRequest('DELETE');

            $controller = \App::make('tdt\core\definitions\DefinitionController');

            $response = $controller->handle("csv/$file");
            $this->assertEquals(200, $response->getStatusCode());
        }

        // Check if everything is deleted properly.
        $definitions_count = Definition::all()->count();
        $csv_count = CsvDefinition::all()->count();

        $this->assertTrue($csv_count == 0);
        $this->assertTrue($definitions_count == 0);
    }
}

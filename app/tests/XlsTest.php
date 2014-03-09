<?php


use tdt\core\definitions\DefinitionController;
use tdt\core\datasets\DatasetController;
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

    public function test_put_api(){

        // Publish each xls file in the test xls data folder.
        foreach($this->test_data as $entry){

            $file = $entry['file'];
            $extension = $entry['extension'];
            $sheet = $entry['sheet'];

            // Set the definition parameters.
            $data = array(
                'description' => "An xls publication from the $file xls file.",
                'sheet' => $sheet,
                'uri' => __DIR__ . "/data/xls/$file" . '.' . $extension,
                'type' => 'xls'
            );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.definition+json'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $controller = \App::make('tdt\core\definitions\DefinitionController');
            $response = $controller->handle("xls/$file");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_get_api(){

        // Request the data for each of the test xls files.
        foreach($this->test_data as $entry){

            $file = $entry['file'];
            $extension = $entry['extension'];
            $sheet = $entry['sheet'];

            $uri = 'xls/'. $file .'.json';
            $this->updateRequest('GET');

            $controller = \App::make('tdt\core\datasets\DatasetController');

            $response = $controller->handle($uri);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_delete_api(){

        // Delete the published definition for each test xls file.
        foreach($this->test_data as $entry){

            $file = $entry['file'];

            $this->updateRequest('DELETE');

            $controller = \App::make('tdt\core\definitions\DefinitionController');

            $response = $controller->handle("xls/$file");
            $this->assertEquals(200, $response->getStatusCode());
        }

        // Check if everything is deleted properly.
        $definitions_count = Definition::all()->count();
        $xls_count = XlsDefinition::all()->count();

        $this->assertTrue($xls_count == 0);
        $this->assertTrue($definitions_count == 0);
    }
}
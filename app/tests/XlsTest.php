<?php


use tdt\core\definitions\DefinitionController;
use tdt\core\datasets\DatasetController;
use Symfony\Component\HttpFoundation\Request;

class XlsTest extends TestCase{

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
            );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.xls'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $response = DefinitionController::handle("xls/$file");

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

            $response = DatasetController::handle($uri);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_delete_api(){

        // Delete the published definition for each test xls file.
        foreach($this->test_data as $entry){

            $file = $entry['file'];

            $this->updateRequest('DELETE');

            $response = DefinitionController::handle("xls/$file");
            $this->assertEquals(200, $response->getStatusCode());
        }
    }
}
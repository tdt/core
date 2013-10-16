<?php


use tdt\core\definitions\DefinitionController;
use Symfony\Component\HttpFoundation\Request;

class CsvTest extends TestCase{

    public function test_put_api(){

        // Set the definition parameters.
        $data = array(
            'documentation' => 'A default CSV publication with comma as a delimiter.',
            'delimiter' => ',',
            'uri' => 'file://' . __DIR__ . '/data/csv/comma_in_quotes.csv',
        );

        // Set the headers.
        $headers = array(
            'Content-Type' => 'application/tdt.csv'
        );

        $method = 'PUT';

        $this->doAPICall($method, $headers, $data);

        // Put the definition controller to the test!
        $response = DefinitionController::handle('csv/comma');

        // Check if the creation of the definition succeeded.
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_get_api(){

        $this->call('GET', 'csv/comma.json');
        $this->assertResponseOk();
    }

    public function test_delete_api(){

        $this->doAPICall('DELETE', array(), array());

        $response = DefinitionController::handle('csv/comma');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
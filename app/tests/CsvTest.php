<?php


use tdt\core\definitions\DefinitionController;
use Symfony\Component\HttpFoundation\Request;

class CsvTest extends TestCase{

    public function test_api(){

        // Prepare the CSV definition properties
        $data = array(
            'documentation' => 'A default CSV publication with comma as a delimiter.',
            'delimiter' => ',',
            'uri' => 'file://' . __DIR__ . '',
        );

        Input::replace($data);

        // Awaiting a fix for
        $this->action('PUT', 'tdt\core\BaseController@handleRequest', array('all' => 'hi/test'));

        exit();
    }
}
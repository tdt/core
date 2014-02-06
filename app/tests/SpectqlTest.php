<?php


use tdt\core\definitions\DefinitionController;
use tdt\core\definitions\SpectqlController;
use tdt\core\datasets\DatasetController;
use Symfony\Component\HttpFoundation\Request;

include(__DIR__ . '/data/spectql/SpectqlQueries.php');

class SpectqlTest extends TestCase{

    public function test_spectql(){


        foreach(SpectqlQueries::$queries as $collection_uri => $tests){

            foreach($tests as $resource_name => $test){

                // Add the definition
                $definition = $test['definition'];
                $definition['uri'] = 'file://' . __DIR__ . $definition['uri'];

                // Set the headers
                $headers = array(
                    'Content-Type' => 'application/tdt.definition+json'
                );

                $this->updateRequest('PUT', $headers, $definition);

                $uri = $collection_uri . '/' . $resource_name;

                // Put the definition controller to the test
                $response = DefinitionController::handle($uri);

                // Check if the creation of the definition succeeded
                $this->assertEquals(200, $response->getStatusCode());


                    // Perform the queries

                    // Compare the results


                // Delete the definition
                $this->updateRequest('DELETE');

                $response = DefinitionController::handle($uri);
                $this->assertEquals(200, $response->getStatusCode());

            }
        }
    }
}
<?php

namespace Tdt\Core\Tests\Api;

use Tdt\Core\Tests\TestCase;
use Tdt\Core\Definitions\DefinitionController;
use Tdt\Core\Datasets\DatasetController;
use Tdt\Core\Tests\Data\Spectql\SpectqlQueries;
use Symfony\Component\HttpFoundation\Request;

class SpectqlTest extends TestCase
{

    public function testSpectql()
    {

        foreach (SpectqlQueries::$queries as $collection_uri => $tests) {

            foreach ($tests as $resource_name => $test) {

                // Add the definition
                $definition = $test['definition'];

                $definition['uri'] = app_path() . '/storage/data/tests/' . $definition['uri'];

                // Set the headers
                $headers = array(
                    'Content-Type' => 'application/tdt.definition+json'
                );

                $this->updateRequest('PUT', $headers, $definition);

                $uri = $collection_uri . '/' . $resource_name;

                // Put the definition controller to the test
                $controller = \App::make('Tdt\Core\Definitions\DefinitionController');
                $response = $controller-> handle($uri);

                // Check if the creation of the definition succeeded
                $this->assertEquals(200, $response->getStatusCode());

                // Fetch the queries from the test
                $queries = $test['queries'];

                // Perform the queries and assert the results
                foreach ($queries as $query) {

                    $query_string = $query['query'];

                    $response = $this->call('GET', $query_string, array(), array(), array(), array());

                    // Our queries are requested in a json format
                    $json = $response->getOriginalContent();
                    $result = json_decode($json);

                    // Compare the result count
                    $this->assertEquals($query['result_count'], count($result));

                    // Compare the first result, only the keys and values
                    $first_result = array_shift($result);

                    $equal_objects = ($first_result == json_decode($query['first_result']));

                    $this->assertTrue($equal_objects);
                }

                // Delete the definition
                $this->updateRequest('DELETE');

                $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

                $response = $controller-> handle($uri);
                $this->assertEquals(200, $response->getStatusCode());
            }
        }
    }
}

<?php

use Tdt\Core\Definitions\DefinitionController;
use Tdt\Core\Datasets\DatasetController;
use Symfony\Component\HttpFoundation\Request;

include(__DIR__ . '/../data/sparql/SparqlQueries.php');

class SparqlTest extends TestCase
{

    public function test_put_api()
    {

        // PUT the sparql definitions via the API
        foreach (\SparqlQueries::$queries as $name => $query) {

            // Set the definition parameters.
            $data = array(
                'description' => "A SPARQL query publication.",
                'endpoint' => 'http://dbpedia.org/sparql',
                'query' => $query,
                'type' => 'sparql'
            );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.definition+json'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');
            $response = $controller->handle("sparql/$name");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_get_api()
    {

        // Request the data for each of the test json files.
        foreach (\SparqlQueries::$queries as $name => $query) {

            $name = 'sparql/'. $name .'.json';
            $this->updateRequest('GET');

            $controller = \App::make('Tdt\Core\Datasets\DatasetController');

            $response = $controller->handle($name);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_update_api()
    {
        foreach (\SparqlQueries::$queries as $name => $query) {

            $updated_description = 'An updated description for ' . $name;

            $identifier = 'sparql/' . $name;

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

    public function test_delete_api()
    {
        // Delete the published definition for each test json file.
        foreach (\SparqlQueries::$queries as $name => $query) {

            $this->updateRequest('DELETE');

            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle("sparql/$name");
            $this->assertEquals(200, $response->getStatusCode());
        }

        // Check if everything is deleted properly.
        $definitions_count = Definition::all()->count();
        $sparql_count = SparqlDefinition::all()->count();

        $this->assertTrue($sparql_count == 0);
        $this->assertTrue($definitions_count == 0);
    }
}

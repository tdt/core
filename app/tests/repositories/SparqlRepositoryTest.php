<?php

namespace Tdt\Core\Tests\Repositories;

use Tdt\Core\Tests\TestCase;
use Tdt\Core\Tests\Data\Sparql\SparqlQueries;
use Symfony\Component\HttpFoundation\Request;

class SparqlDefinitionRepositoryTest extends TestCase
{

    public function testPut()
    {
        // Publish each SPARQL file in the test csv data folder.
        foreach (SparqlQueries::$queries as $name => $query) {

            // Set the definition parameters.
            $input = array(
                'description' => "A SPARQL query publication called " . $name,
                'endpoint' => 'http://dbpedia.org/sparql',
                'query' => $query,
            );

            // Test the SparqlDefinitionRepository
            $sparql_repository = \App::make('Tdt\Core\Repositories\Interfaces\SparqlDefinitionRepositoryInterface');

            $sparql_definition = $sparql_repository->store($input);

            // Check for properties
            foreach ($input as $property => $value) {
                $this->assertEquals($value, $sparql_definition[$property]);
            }
        }
    }

    public function testGet()
    {

        $sparql_repository = $sparql_repository = \App::make('Tdt\Core\Repositories\Interfaces\SparqlDefinitionRepositoryInterface');

        $all = $sparql_repository->getAll();

        $this->assertEquals(count(SparqlQueries::$queries), count($all));

        foreach ($all as $sparql_definition) {

            // Test the getById
            $sparql_definition_clone = $sparql_repository->getById($sparql_definition['id']);

            $this->assertEquals($sparql_definition, $sparql_definition_clone);
        }

        // Test against the properties we've stored
        foreach (SparqlQueries::$queries as $name => $query) {

            $sparql_definition = array_shift($all);

            $this->assertEquals($sparql_definition['description'], "A SPARQL query publication called " . $name);
            $this->assertEquals($sparql_definition['endpoint'], 'http://dbpedia.org/sparql');
            $this->assertEquals($sparql_definition['query'], $query);
        }
    }

    public function testUpdate()
    {

        $sparql_repository = \App::make('Tdt\Core\Repositories\Interfaces\SparqlDefinitionRepositoryInterface');

        $all = $sparql_repository->getAll();

        foreach ($all as $sparql_definition) {

            $updated_description = 'An updated description for object with description: ' . $sparql_definition['description'];

            $updated_definition = $sparql_repository->update($sparql_definition['id'], array('description' => $updated_description));

            $this->assertEquals($updated_definition['description'], $updated_description);
        }
    }

    public function testDelete()
    {

        $sparql_repository = \App::make('Tdt\Core\Repositories\Interfaces\SparqlDefinitionRepositoryInterface');

        $all = $sparql_repository->getAll();

        foreach ($all as $sparql_definition) {

            $result = $sparql_repository->delete($sparql_definition['id']);

            $this->assertTrue($result);
        }
    }

    public function testHelpFunctions()
    {
        $sparql_repository = \App::make('Tdt\Core\Repositories\Interfaces\SparqlDefinitionRepositoryInterface');

        $this->assertTrue(is_array($sparql_repository->getCreateParameters()));
        $this->assertTrue(is_array($sparql_repository->getAllParameters()));
    }
}

<?php

namespace Tdt\Core\Tests\Repositories;

use Tdt\Core\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JsonDefinitionRepositoryTest extends TestCase
{

    // This array holds the names of the files that can be used
    // to test the json definitions.
    private $test_data = array(
        'complex_persons',
        'simple_persons',
    );

    public function testPut()
    {
        // Publish each CSV file in the test json data folder.
        foreach ($this->test_data as $file) {

            // Set the definition parameters.
            $input = array(
                'description' => "A JSON publication from the $file json file.",
                'uri' => app_path() . "/storage/data/tests/json/$file.json",
            );

            // Test the JsonDefinitionRepository
            $json_repository = \App::make('Tdt\Core\Repositories\Interfaces\JsonDefinitionRepositoryInterface');

            $json_definition = $json_repository->store($input);

            // Check for properties
            foreach ($input as $property => $value) {
                $this->assertEquals($value, $json_definition[$property]);
            }
        }
    }

    public function testGet()
    {

        $json_repository = $json_repository = \App::make('Tdt\Core\Repositories\Interfaces\JsonDefinitionRepositoryInterface');

        $all = $json_repository->getAll();

        $this->assertEquals(count($this->test_data), count($all));

        foreach ($all as $json_definition) {

            // Test the getById
            $json_definition_clone = $json_repository->getById($json_definition['id']);

            $this->assertEquals($json_definition, $json_definition_clone);
        }

        // Test against the properties we've stored
        foreach ($this->test_data as $file) {

            $json_definition = array_shift($all);

            $this->assertEquals($json_definition['description'], "A JSON publication from the $file json file.");

            $this->assertEquals($json_definition['uri'], app_path() . "/storage/data/tests/json/$file.json");
        }
    }

    public function testUpdate()
    {

        $json_repository = \App::make('Tdt\Core\Repositories\Interfaces\JsonDefinitionRepositoryInterface');

        $all = $json_repository->getAll();

        foreach ($all as $json_definition) {

            $updated_description = 'An updated description for object with description: ' . $json_definition['description'];

            $updated_definition = $json_repository->update($json_definition['id'], array('description' => $updated_description));

            $this->assertEquals($updated_definition['description'], $updated_description);
        }
    }

    public function testDelete()
    {

        $json_repository = \App::make('Tdt\Core\Repositories\Interfaces\JsonDefinitionRepositoryInterface');

        $all = $json_repository->getAll();

        foreach ($all as $json_definition) {

            $result = $json_repository->delete($json_definition['id']);

            $this->assertTrue($result);
        }
    }

    public function testHelpFunctions()
    {

        $json_repository = \App::make('Tdt\Core\Repositories\Interfaces\JsonDefinitionRepositoryInterface');

        $this->assertTrue(is_array($json_repository->getCreateParameters()));
        $this->assertTrue(is_array($json_repository->getAllParameters()));
    }
}

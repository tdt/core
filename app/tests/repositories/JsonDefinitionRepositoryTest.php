<?php

use Symfony\Component\HttpFoundation\Request;

class JsonDefinitionRepositoryTest extends TestCase{

    // This array holds the names of the files that can be used
    // to test the json definitions.
    private $test_data = array(
        'complex_persons',
        'simple_persons',
    );

    public function test_put()
    {
        // Publish each CSV file in the test json data folder.
        foreach ($this->test_data as $file) {

            // Set the definition parameters.
            $input = array(
                'description' => "A JSON publication from the $file json file.",
                'uri' => 'file://' . __DIR__ . "/../data/json/$file.json",
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

    public function test_get()
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

            $this->assertEquals($json_definition['uri'], 'file://' . __DIR__ . "/../data/json/$file.json");
        }
    }

    public function test_update()
    {

        $json_repository = \App::make('Tdt\Core\Repositories\Interfaces\JsonDefinitionRepositoryInterface');

        $all = $json_repository->getAll();

        foreach ($all as $json_definition) {

            $updated_description = 'An updated description for object with description: ' . $json_definition['description'];

            $updated_definition = $json_repository->update($json_definition['id'], array('description' => $updated_description));

            $this->assertEquals($updated_definition['description'], $updated_description);
        }
    }

    public function test_delete()
    {

        $json_repository = \App::make('Tdt\Core\Repositories\Interfaces\JsonDefinitionRepositoryInterface');

        $all = $json_repository->getAll();

        foreach ($all as $json_definition) {

            $result = $json_repository->delete($json_definition['id']);

            $this->assertTrue($result);
        }
    }

    public function test_help_functions()
    {

        $json_repository = \App::make('Tdt\Core\Repositories\Interfaces\JsonDefinitionRepositoryInterface');

        $this->assertTrue(is_array($json_repository->getCreateParameters()));
        $this->assertTrue(is_array($json_repository->getAllParameters()));
    }
}

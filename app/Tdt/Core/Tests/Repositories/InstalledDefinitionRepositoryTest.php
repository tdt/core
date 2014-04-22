<?php

namespace Tdt\Core\Tests\Repositories;

use Tdt\Core\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class InstalledDefinitionRepositoryTest extends TestCase
{

    // This array holds the names of the files that can be used
    // to test the json definitions.
    private $test_data = array(
        'example1' => array(
            'class' => 'Example.php',
            'path' =>  '/storage/data/tests/installed/Example.php',
        ),
    );

    public function testPut()
    {
        foreach ($this->test_data as $config) {

            // Set the definition parameters.
            $input = array(
                'description' => "Data retrieved from an installed resource",
                'class' => $config['class'],
                'path' => app_path() . $config['path'],
            );

            include_once($input['path']);

            // Test the XmlDefinitionRepository
            $installed = \App::make('Tdt\Core\Repositories\Interfaces\InstalledDefinitionRepositoryInterface');

            $installed_definition = $installed->store($input);

            $this->assertNotEmpty($installed_definition);

            if (!empty($installed_definition)) {
                // Check for properties
                foreach ($input as $property => $value) {
                    $this->assertEquals($value, $installed_definition[$property]);
                }
            }
        }
    }

    public function testGet()
    {
        $installed = \App::make('Tdt\Core\Repositories\Interfaces\InstalledDefinitionRepositoryInterface');

        $all = $installed->getAll();

        $this->assertEquals(count($this->test_data), count($all));

        foreach ($all as $installed_definition) {

            // Test the getById function
            $installed_definition_clone = $installed->getById($installed_definition['id']);

            $this->assertEquals($installed_definition, $installed_definition_clone);
        }
    }

    public function testUpdate()
    {
        $installed = \App::make('Tdt\Core\Repositories\Interfaces\InstalledDefinitionRepositoryInterface');

        $all = $installed->getAll();

        foreach ($all as $installed_definition) {

            $updated_description = 'An updated description for object with description: ' . $installed_definition['description'];

            $updated_definition = $installed->update($installed_definition['id'], array('description' => $updated_description));

            $this->assertEquals($updated_definition['description'], $updated_description);
        }
    }

    public function testDelete()
    {
        $installed_repository = \App::make('Tdt\Core\Repositories\Interfaces\InstalledDefinitionRepositoryInterface');

        $all = $installed_repository->getAll();

        foreach ($all as $installed_definition) {

            $result = $installed_repository->delete($installed_definition['id']);

            $this->assertTrue($result);
        }
    }
}

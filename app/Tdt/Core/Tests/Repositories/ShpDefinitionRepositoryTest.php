<?php

namespace Tdt\Core\Tests\Repositories;

use Tdt\Core\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ShpDefinitionRepositoryTest extends TestCase
{

    // This array holds the names of the files that can be used
    // to test the shp definitions.
    private $test_data = array(
        array(
            'name' => 'boundaries',
            'file' => 'gis.osm_boundaries_v06',
            'epsg' => 4326,
        ),
    );

    public function testPut()
    {
        // Publish each CSV file in the test shp data folder.
        foreach ($this->test_data as $entry) {

            $file = $entry['file'];

            // Set the definition parameters.
            $input = array(
                'description' => "A shp publication from the $file shp file.",
                'epsg' => '4326',
                'uri' => app_path() . "/storage/data/tests/shp/$file.shp",
            );

            // Test the ShpDefinitionRepository
            $shp_repository = \App::make('Tdt\Core\Repositories\Interfaces\ShpDefinitionRepositoryInterface');

            $shp_definition = $shp_repository->store($input);

            // Check for properties
            foreach ($input as $property => $value) {
                $this->assertEquals($value, $shp_definition[$property]);
            }
        }
    }

    public function testGet()
    {
        $shp_repository = $shp_repository = \App::make('Tdt\Core\Repositories\Interfaces\ShpDefinitionRepositoryInterface');

        $all = $shp_repository->getAll();

        $this->assertEquals(count($this->test_data), count($all));

        foreach ($all as $shp_definition) {

            // Test the getById
            $shp_definition_clone = $shp_repository->getById($shp_definition['id']);

            $this->assertEquals($shp_definition, $shp_definition_clone);
        }

        // Test against the properties we've stored
        foreach ($this->test_data as $entry) {

            $file = $entry['file'];

            $shp_definition = array_shift($all);

            $this->assertEquals($shp_definition['description'], "A shp publication from the $file shp file.");
            $this->assertEquals($shp_definition['epsg'], 4326);
            $this->assertEquals($shp_definition['uri'], app_path() . "/storage/data/tests/shp/$file.shp");
        }
    }

    public function testUpdate()
    {

        $shp_repository = \App::make('Tdt\Core\Repositories\Interfaces\ShpDefinitionRepositoryInterface');

        $all = $shp_repository->getAll();

        foreach ($all as $shp_definition) {
            $updated_description = 'An updated description for object with description: ' . $shp_definition['description'];

            $updated_definition = $shp_repository->update($shp_definition['id'], array('description' => $updated_description));

            $this->assertEquals($updated_definition['description'], $updated_description);
        }
    }

    public function testDelete()
    {

        $shp_repository = \App::make('Tdt\Core\Repositories\Interfaces\ShpDefinitionRepositoryInterface');

        $all = $shp_repository->getAll();

        foreach ($all as $shp_definition) {
            $result = $shp_repository->delete($shp_definition['id']);

            $this->assertTrue($result);
        }
    }

    public function testHelpFunctions()
    {
        $shp_repository = \App::make('Tdt\Core\Repositories\Interfaces\ShpDefinitionRepositoryInterface');

        $this->assertTrue(is_array($shp_repository->getCreateParameters()));
        $this->assertTrue(is_array($shp_repository->getAllParameters()));
    }
}

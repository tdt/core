<?php

namespace Tdt\Core\Tests\Repositories;

use Tdt\Core\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class XlsDefinitionRepositoryTest extends TestCase
{

    // This array holds the names of the files that can be used
    // to test the csv definitions.
    private $test_data = array(
        array(
            'file' => 'tabular',
            'extension' => 'xlsx',
            'sheet' => 'Sheet1',
        ),
    );

    public function testPut()
    {

        // Publish each XLS file in the test csv data folder.
        foreach ($this->test_data as $entry) {

            $file = $entry['file'];
            $extension = $entry['extension'];
            $sheet = $entry['sheet'];

            // Set the definition parameters.
            $input = array(
                'description' => "An XLS publication from the $file xls file.",
                'sheet' => $sheet,
                'uri' => app_path() . "/storage/data/tests/xls/$file" . '.' . $extension,
                'type' => 'XLS'
            );

            // Test the XlsDefinitionRepository
            $xls_repository = \App::make('Tdt\Core\Repositories\Interfaces\XlsDefinitionRepositoryInterface');

            $xls_definition = $xls_repository->store($input);

            // Check for properties
            foreach ($input as $property => $value) {
                $this->assertEquals($value, $xls_definition[$property]);
            }
        }
    }

    public function testGet()
    {

        $xls_repository = $xls_repository = \App::make('Tdt\Core\Repositories\Interfaces\XlsDefinitionRepositoryInterface');

        $all = $xls_repository->getAll();

        $this->assertEquals(count($this->test_data), count($all));

        foreach ($all as $xls_definition) {

            // Test the getById
            $xls_definition_clone = $xls_repository->getById($xls_definition['id']);

            $this->assertEquals($xls_definition, $xls_definition_clone);
        }

        // Test against the properties we've stored
        foreach ($this->test_data as $entry) {

            $file = $entry['file'];
            $extension = $entry['extension'];
            $sheet = $entry['sheet'];

            $xls_definition = array_shift($all);

            $this->assertEquals($xls_definition['description'], "An XLS publication from the $file xls file.");
            $this->assertEquals($xls_definition['sheet'], $sheet);
            $this->assertEquals($xls_definition['uri'], app_path() . "/storage/data/tests/xls/$file" . '.' . $extension);
        }
    }

    public function testUpdate()
    {

        $xls_repository = \App::make('Tdt\Core\Repositories\Interfaces\XlsDefinitionRepositoryInterface');

        $all = $xls_repository->getAll();

        foreach ($all as $xls_definition) {

            $updated_description = 'An updated description for object with description: ' . $xls_definition['description'];

            $updated_definition = $xls_repository->update($xls_definition['id'], array('description' => $updated_description));

            $this->assertEquals($updated_definition['description'], $updated_description);
        }
    }

    public function testDelete()
    {

        $xls_repository = \App::make('Tdt\Core\Repositories\Interfaces\XlsDefinitionRepositoryInterface');

        $all = $xls_repository->getAll();

        foreach ($all as $xls_definition) {

            $result = $xls_repository->delete($xls_definition['id']);

            $this->assertTrue($result);
        }
    }

    public function testHelpFunctions()
    {

        $xls_repository = \App::make('Tdt\Core\Repositories\Interfaces\XlsDefinitionRepositoryInterface');

        $this->assertTrue(is_array($xls_repository->getCreateParameters()));
        $this->assertTrue(is_array($xls_repository->getAllParameters()));
    }
}

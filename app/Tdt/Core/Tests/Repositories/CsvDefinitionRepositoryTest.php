<?php

namespace Tdt\Core\Tests\Repositories;

use Tdt\Core\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class CsvDefinitionRepositoryTest extends TestCase
{

    // This array holds the names of the files that can be used
    // to test the csv definitions.
    private $test_data = array(
        'comma_in_quotes',
        'escaped_quotes',
        'json',
        'latin1',
        'newlines',
        'quotes_and_newlines',
        'simple',
        'utf8',
    );

    public function testPut()
    {

        // Publish each CSV file in the test csv data folder.
        foreach ($this->test_data as $file) {

            // Set the definition parameters.
            $input = array(
                'description' => "A CSV publication from the $file csv file.",
                'delimiter' => ',',
                'uri' => app_path() . "/storage/data/tests/csv/$file.csv",
            );

            // Test the CsvDefinitionRepository
            $csv_repository = \App::make('Tdt\Core\Repositories\Interfaces\CsvDefinitionRepositoryInterface');

            $csv_definition = $csv_repository->store($input);

            // Check for properties
            foreach ($input as $property => $value) {
                $this->assertEquals($value, $csv_definition[$property]);
            }
        }
    }

    public function testGet()
    {

        $csv_repository = $csv_repository = \App::make('Tdt\Core\Repositories\Interfaces\CsvDefinitionRepositoryInterface');

        $all = $csv_repository->getAll();

        $this->assertEquals(count($this->test_data), count($all));

        foreach ($all as $csv_definition) {

            // Test the getById
            $csv_definition_clone = $csv_repository->getById($csv_definition['id']);

            $this->assertEquals($csv_definition, $csv_definition_clone);
        }

        // Test against the properties we've stored
        foreach ($this->test_data as $file) {

            $csv_definition = array_shift($all);

            $this->assertEquals($csv_definition['description'], "A CSV publication from the $file csv file.");
            $this->assertEquals($csv_definition['delimiter'], ',');
            $this->assertEquals($csv_definition['uri'], app_path() . "/storage/data/tests/csv/$file.csv");
        }
    }

    public function testUpdate()
    {

        $csv_repository = \App::make('Tdt\Core\Repositories\Interfaces\CsvDefinitionRepositoryInterface');

        $all = $csv_repository->getAll();

        foreach ($all as $csv_definition) {

            $updated_description = 'An updated description for object with description: ' . $csv_definition['description'];

            $updated_definition = $csv_repository->update($csv_definition['id'], array('description' => $updated_description));

            $this->assertEquals($updated_definition['description'], $updated_description);
        }
    }

    public function testDelete()
    {

        $csv_repository = \App::make('Tdt\Core\Repositories\Interfaces\CsvDefinitionRepositoryInterface');

        $all = $csv_repository->getAll();

        foreach ($all as $csv_definition) {

            $result = $csv_repository->delete($csv_definition['id']);

            $this->assertTrue($result);
        }
    }

    public function testHelpFunctions()
    {

        $csv_repository = \App::make('Tdt\Core\Repositories\Interfaces\CsvDefinitionRepositoryInterface');

        $this->assertTrue(is_array($csv_repository->getCreateParameters()));
        $this->assertTrue(is_array($csv_repository->getAllParameters()));
    }
}

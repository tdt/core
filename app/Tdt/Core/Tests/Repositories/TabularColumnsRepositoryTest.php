<?php

namespace Tdt\Core\Tests\Repositories;

use Tdt\Core\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class TabularColumnsRepositoryTest extends TestCase
{

    private $amount_of_total_columns = 4;

    private $test_data = array(
        array(
            'tabular_id' => 1,
            'tabular_type' => 'CsvDefinition',
            'columns' => array(
                array(
                    'index' => 0,
                    'column_name' => 'column one',
                    'column_name_alias' => 'column wan',
                    'is_pk' => 0,
                ),
                array(
                    'index' => 1,
                    'column_name' => 'column two',
                    'column_name_alias' => 'column twoe',
                    'is_pk' => 0,
                ),
            ),
        ),
        array(
            'tabular_id' => 1,
            'tabular_type' => 'ShpDefinition',
            'columns' => array(
                array(
                    'index' => 0,
                    'column_name' => 'column one in shp',
                    'column_name_alias' => 'column wan in shp',
                    'is_pk' => 0,
                ),
                array(
                    'index' => 1,
                    'column_name' => 'column two in shp',
                    'column_name_alias' => 'column twoe in shp',
                    'is_pk' => 0,
                ),
            ),
        ),
    );

    public function testPut()
    {

        $tab_column_repository = \App::make('Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface');

        foreach ($this->test_data as $column) {

            $input = array(
                'tabular_id' => $column['tabular_id'],
                'tabular_type' => $column['tabular_type'],
                );

            foreach ($column['columns'] as $column_info) {

                $input = array_merge($input, $column_info);

                $stored_column = $tab_column_repository->store($input);

                foreach ($input as $key => $value) {
                    $this->assertEquals($value, $stored_column[$key]);
                }
            }
        }
    }

    public function testGet()
    {

        // Make sure we have the same amount of columns we have put in
        $tab_column_repository = \App::make('Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface');

        $this->assertEquals($this->amount_of_total_columns, count($tab_column_repository->getAll()));

        // Test the equality of the properties
        $index = 0;

        $all_columns = $tab_column_repository->getAll();

        foreach ($this->test_data as $column) {

            $input = array(
                'tabular_id' => $column['tabular_id'],
                'tabular_type' => $column['tabular_type'],
            );

            foreach ($column['columns'] as $column_info) {

                $input = array_merge($input, $column_info);

                $tab_column = $all_columns[$index];

                foreach ($input as $key => $value) {
                    $this->assertEquals($value, $tab_column[$key]);
                }

                $index++;
            }
        }

        // Test aliases, retrieval of specific columns
        foreach ($this->test_data as $column) {

            $input = array(
                'tabular_id' => $column['tabular_id'],
                'tabular_type' => $column['tabular_type'],
            );

            // Test the aliases
            $aliases = $tab_column_repository->getColumnAliases($column['tabular_id'], $column['tabular_type']);

            $this->assertEquals(count($column['columns']), count($aliases));

            foreach ($column['columns'] as $column_info) {

                $this->assertTrue(array_key_exists($column_info['column_name'], $aliases));
                $this->assertEquals($column_info['column_name_alias'], $aliases[$column_info['column_name']]);
            }

            // Test the retrieval of the columns for a specific tabular source
            $columns = $tab_column_repository->getColumns($column['tabular_id'], $column['tabular_type']);

            // Test the amount of columns retrieved
            $this->assertEquals(count($column['columns']), count($columns));

            // Test the properties
            $index = 0;

            foreach ($column['columns'] as $column_info) {

                $tab_column = $columns[$index];

                $input = array_merge($input, $column_info);

                foreach ($input as $key => $value) {
                    $this->assertEquals($value, $tab_column[$key]);
                }

                $index++;
            }

        }
    }

    public function testDelete()
    {

        $tab_column_repository = \App::make('Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface');

        foreach ($this->test_data as $column) {

            $tabular_id = $column['tabular_id'];
            $tabular_type = $column['tabular_type'];

            $tab_column_repository->deleteBulk($tabular_id, $tabular_type);
            $this->assertEquals(0, count($tab_column_repository->getColumns($tabular_id, $tabular_type)));
        }
    }

    public function testHelpFunctions()
    {

        $tab_column_repository = \App::make('Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface');

        $this->assertTrue(is_array($tab_column_repository->getCreateParameters()));
    }
}

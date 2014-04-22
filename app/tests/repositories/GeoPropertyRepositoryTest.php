<?php

namespace Tdt\Core\Tests\Repositories;

use Tdt\Core\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class GeoPropertyRepositoryTest extends TestCase
{

    private $amount_of_total_columns = 4;

    private $test_data = array(
        array(
            'source_id' => 1,
            'source_type' => 'CsvDefinition',
            'geo_props' => array(
                array(
                    'path' => 0,
                    'property' => 'column one',
                ),
                array(
                    'path' => 1,
                    'property' => 'column two',
                ),
            ),
        ),
        array(
            'source_id' => 1,
            'source_type' => 'ShpDefinition',
            'geo_props' => array(
                array(
                    'path' => 0,
                    'property' => 'column one in shp',
                ),
                array(
                    'path' => 1,
                    'property' => 'column two in shp',
                ),
            ),
        ),
    );

    public function testPut()
    {
        $geo_property_repository = \App::make('Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface');

        foreach ($this->test_data as $geo_property) {

            $input = array(
                'source_id' => $geo_property['source_id'],
                'source_type' => $geo_property['source_type'],
                );

            foreach ($geo_property['geo_props'] as $geo_info) {

                $input = array_merge($input, $geo_info);

                $stored_geo = $geo_property_repository->store($input);

                foreach ($input as $key => $value) {
                    $this->assertEquals($value, $stored_geo[$key]);
                }
            }
        }
    }

    public function testGet()
    {
        // Make sure we have the same amount of columns we have put in
        $geo_property_repository = \App::make('Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface');

        $this->assertEquals($this->amount_of_total_columns, count($geo_property_repository->getAll()));

        // Test the equality of the properties
        $index = 0;

        $all_columns = $geo_property_repository->getAll();

        foreach ($this->test_data as $geo) {

            $input = array(
                'source_id' => $geo['source_id'],
                'source_type' => $geo['source_type'],
            );

            foreach ($geo['geo_props'] as $geo_info) {

                $input = array_merge($input, $geo_info);

                $tab_column = $all_columns[$index];

                foreach ($input as $key => $value) {
                    $this->assertEquals($value, $tab_column[$key]);
                }

                $index++;
            }
        }

        // Test aliases, retrieval of specific geo properties
        foreach ($this->test_data as $geo) {

            $input = array(
                'source_id' => $geo['source_id'],
                'source_type' => $geo['source_type'],
            );

            // Test the retrieval of the geo properties for a specific tabular source
            $geo_properties = $geo_property_repository->getGeoProperties($geo['source_id'], $geo['source_type']);

            // Test the amount of geo properties retrieved
            $this->assertEquals(count($geo['geo_props']), count($geo_properties));

            // Test the properties
            $index = 0;

            foreach ($geo['geo_props'] as $geo_info) {

                $tab_column = $geo_properties[$index];

                $input = array_merge($input, $geo_info);

                foreach ($input as $key => $value) {
                    $this->assertEquals($value, $tab_column[$key]);
                }

                $index++;
            }
        }
    }

    public function testDelete()
    {
        $geo_property_repository = \App::make('Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface');

        foreach ($this->test_data as $geo) {

            $source_id = $geo['source_id'];
            $source_type = $geo['source_type'];

            $geo_property_repository->deleteBulk($source_id, $source_type);
            $this->assertEquals(0, count($geo_property_repository->getGeoProperties($source_id, $source_type)));
        }
    }

    public function testHelpFunctions()
    {
        $geo_property_repository = \App::make('Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface');

        $this->assertTrue(is_array($geo_property_repository->getCreateParameters()));
    }
}

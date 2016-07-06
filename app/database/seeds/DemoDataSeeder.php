<?php

/**
 * This class seeds the back-end with several definitions with different source types.
 * We use the data from the tests folder as default data sets.
 */

use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;

class DemoDataSeeder extends Seeder
{

    public function __construct(DefinitionRepositoryInterface $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        // Add csv definitions
        $this->seedCsv();

        // Add the json definitions
        $this->seedJson();

        // Add the xml definitions
        $this->seedXml();

        // Add the shp definitions
        $this->seedShp();

        // Add the xls definitions
        $this->seedXls();
    }

    /**
     * Seed the CSV definitions
     */
    private function seedCsv()
    {
        $csv_config = [
            'collection_uri' => 'afghanistan',
            'resource_name' => 'provinces',
            'title' => "Afghanistan provinces and districts",
            'description' => 'Geographical data about Afghanistan concerning provinces and districts.',
            'uri' => 'file://' . app_path() . '/storage/data/csv/geo.csv',
            'delimiter' => ';',
            'has_header_row' => 1,
            'start_row' => 0,
            'type' => 'csv',
            'rights' => 'License Not Specified',
            'keywords' => 'afghanistan, geographical',
            'columns' => array(
                array(
                    'column_name' => 'lon',
                    'index' => 0,
                    'column_name_alias' => 'lon',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'lat',
                    'index' => 1,
                    'column_name_alias' => 'lat',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'Unit_Type',
                    'index' => 2,
                    'column_name_alias' => 'Unit_Type',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'Dist_Name',
                    'index' => 3,
                    'column_name_alias' => 'Dist_Name',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'Prov_Name',
                    'index' => 4,
                    'column_name_alias' => 'Prov_Name',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'Dist_ID',
                    'index' => 5,
                    'column_name_alias' => 'Dist_ID',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'Prov_ID',
                    'index' => 6,
                    'column_name_alias' => 'Prov_ID',
                    'is_pk' => 0
                    ),
                )
            ];

        $definition = Definition::where('collection_uri', '=', 'afghanistan')->where('resource_name', '=', 'provinces')->first();

        if (empty($definition)) {
            $this->definitions->store($csv_config);

            $this->command->info("Published a CSV dataset.");
            $added = true;
        } else {
             $this->command->info("No CSV files have been published, all of the uri's that the CSV seeder wanted to use are already taken.");
        }
    }

    /**
     * Seed the XML definitions
     */
    private function seedXml()
    {
        $xml_config = [
            'collection_uri' => 'fiction',
            'resource_name' => 'persons',
            'uri' => app_path() . '/storage/data/xml/persons.xml',
            'description' => 'An XML dataset about fictional persons',
            'title' => 'Fiction persons',
            'type' => 'xml',
            'rights' => 'License Not Specified',
            'keywords' => 'people'
        ];

        $definition = Definition::where('collection_uri', '=', 'fiction')->where('resource_name', '=', 'persons')->first();

        if (empty($definition)) {
            $this->definitions->store($xml_config);
            $this->command->info("Published an XML dataset.");
        } else {
            $this->command->info("No XML files have been published, all of the uri's that the XML seeder wanted to use are already taken.");
        }
    }

    /**
     * Seed the JSON definitions
     */
    private function seedJson()
    {
        // The json file names
        $json_config = [
            'collection_uri' => 'uk',
            'resource_name' => 'crime',
            'description' => 'Crime data from the UK.',
            'title' => 'UK Crime',
            'uri' => 'file://' . app_path() . '/storage/data/json/crime.json',
            'type' => 'json',
            'json_type' => 'Plain',
            'rights' => 'License Not Specified',
            'keywords' => 'UK, crime'
        ];

        $definition = Definition::where('collection_uri', '=', 'uk')->where('resource_name', '=', 'crime')->first();

        if (empty($definition)) {
            $this->definitions->store($json_config);
            $this->command->info("Published a JSON dataset.");
        } else {
            $this->command->info("No JSON files have been published, all of the uri's that the JSON seeder wanted to use are already taken.");
        }
    }

    /**
     * Seed the XLS definitions
     */
    private function seedXls()
    {
        $xls_config = [
            'collection_uri' => 'baseball',
            'resource_name' => '2008',
            'uri' => app_path() . '/storage/data/xls/baseball.xlsx',
            'title' => '2008 Major League Baseball data',
            'sheet' => 'Sheet1',
            'type' => 'xls',
            'description' => 'Individual offensive statistics from the 2008 Major League Baseball season.',
            'columns' => array(
                array(
                    'column_name' => 'Player',
                    'index' => 0,
                    'column_name_alias' => 'Player',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'Id',
                    'index' => 1,
                    'column_name_alias' => 'Id',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'Salary',
                    'index' => 2,
                    'column_name_alias' => 'Salary',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'Rookie',
                    'index' => 3,
                    'column_name_alias' => 'Rookie',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'Position',
                    'index' => 4,
                    'column_name_alias' => 'Position',
                    'is_pk' => 0
                    ),
            ),
            'rights' => 'License Not Specified',
            'keywords' => 'Baseball, USA',
        ];


        $definition = Definition::where('collection_uri', '=', 'baseball')->where('resource_name', '=', '2008')->first();

        if (empty($definition)) {
            $this->definitions->store($xls_config);
            $this->command->info("Published an XLS dataset.");
        } else {
            $this->command->info("No XLS files have been published, all of the uri's that the XLS seeder wanted to use are already taken.");
        }
    }

    /**
     * Seed the SHP definitions
     */
    private function seedShp()
    {
        $shp_config = [
            'collection_uri' => 'dresden',
            'resource_name' => 'rivers',
            'title' => 'Dresden rivers',
            'uri' => app_path() . '/storage/data/shp/rivers/gis.osm_boundaries_v06.shp',
            'epsg' => 4326,
            'type' => 'shp',
            'rights' => 'License Not Specified',
            'keywords' => 'dresden, geographical',
            'description' => 'Shape file containing the rivers in the city Dresden.',
            'columns' => array(
                array(
                    'column_name' => 'osm_id',
                    'index' => 0,
                    'column_name_alias' => 'osm_id',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'lastchange',
                    'index' => 1,
                    'column_name_alias' => 'lastchange',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'code',
                    'index' => 2,
                    'column_name_alias' => 'code',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'fclass',
                    'index' => 3,
                    'column_name_alias' => 'fclass',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'deleted',
                    'index' => 4,
                    'column_name_alias' => 'deleted',
                    'is_pk' => 0
                    ),
                array(
                    'column_name' => 'parts',
                    'index' => 5,
                    'column_name_alias' => 'parts',
                    'is_pk' => 0
                    ),
                ),
            'geo' => array(
                array(
                    'path' => 'parts',
                    'property' => 'polyline',
                    ),
                ),
            ];

        $definition = Definition::where('collection_uri', '=', 'dresden')->where('resource_name', '=', 'rivers')->first();

        if (empty($definition)) {
            $this->definitions->store($shp_config);
            $this->command->info("Published a SHP dataset.");
        } else {
            $this->command->info("No SHP files have been published, all of the uri's that the SHP seeder wanted to use are already taken.");
        }
    }
}

<?php

/**
 * This class seeds the back-end with several definitions with different source types.
 * We use the data from the tests folder as default data sets.
 */

class DemoDataSeeder extends Seeder
{

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

        // The csv file names
        $csv_data = array(
            'geo' => array(
                    'description' => 'Geographical data about Afghanistan concerning provinces and districts.',
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

                ),
        );

        // Provide a message when nothing has been added (doubles have been found)
        $added = false;

        foreach ($csv_data as $file => $definition_info) {

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'csv')->where('resource_name', '=', $file)->first();

            if (empty($definition)) {

                // Create a new CsvDefinition
                $csv_def = new CsvDefinition();
                $csv_def->description = $definition_info['description'];
                $csv_def->uri = 'file://' . app_path() . '/storage/data/csv/' . $file . '.csv';
                $csv_def->delimiter = ';';
                $csv_def->has_header_row = 1;
                $csv_def->start_row = 0;
                $csv_def->save();

                // Add the tabular columns
                foreach ($definition_info['columns'] as $column) {

                    $tab_column = new TabularColumns();
                    $tab_column->column_name = $column['column_name'];
                    $tab_column->index = $column['index'];
                    $tab_column->column_name_alias = $column['column_name_alias'];
                    $tab_column->is_pk = $column['is_pk'];
                    $tab_column->tabular_id = $csv_def->id;
                    $tab_column->tabular_type = 'CsvDefinition';
                    $tab_column->save();
                }

                // Add the CsvDefinition to a Definition
                $definition = new Definition();
                $definition->collection_uri = 'csv';
                $definition->resource_name = $file;
                $definition->source_id = $csv_def->id;
                $definition->source_type = 'CsvDefinition';
                $definition->draft = false;
                $definition->save();

                $this->command->info("Published a CSV file with name $file on uri (relative to the root) csv/$file .");
                $added = true;
            }
        }

        if (!$added) {
            $this->command->info("No CSV files have been published, all of the uri's that the CSV seeder wanted to use are already taken.");
        }
    }

    /**
     * Seed the XML definitions
     */
    private function seedXml()
    {

        $xml_data = array(
            'persons' => 'Auto-generated xml file about persons.',
        );

        $added = false;

        foreach ($xml_data as $file => $description) {

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'xml')->where('resource_name', '=', $file)->first();

            if (empty($definition)) {

                // Create a new XmlDefinition
                $xml_def = new XmlDefinition();
                $xml_def->uri = app_path() . '/storage/data/xml/' . $file . '.xml';
                $xml_def->description = $description;
                $xml_def->save();

                // Add the XmlDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = 'xml';
                $definition->resource_name = $file;
                $definition->source_id = $xml_def->id;
                $definition->source_type = 'XmlDefinition';
                $definition->draft = false;
                $definition->save();

                $this->command->info("Published an XML file with file name $file on uri (relative to the root) xml/$file .");
                $added = true;
            }
        }

        if (!$added) {
            $this->command->info("No XML files have been published, all of the uri's that the XML seeder wanted to use are already taken.");
        }

    }

    /**
     * Seed the JSON definitions
     */
    private function seedJson()
    {

        // The json file names
        $json_data = array(
            'crime' => 'Crime data from the uk.',
        );

        $added = false;

        foreach ($json_data as $file => $description) {

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'json')->where('resource_name', '=', $file)->first();

            if (empty($definition)) {

                // Create a new JsonDefinition
                $json_def = new JsonDefinition();
                $json_def->uri = 'file://' . app_path() . '/storage/data/json/' . $file . '.json';
                $json_def->description = $description;
                $json_def->save();

                // Add the JsonDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = 'json';
                $definition->resource_name = $file;
                $definition->source_id = $json_def->id;
                $definition->source_type = 'JsonDefinition';
                $definition->draft = false;
                $definition->save();

                $this->command->info("Published a JSON file, $file, on uri (relative to the root) json/$file .");
                $added = true;
            }
        }

        if (!$added) {
            $this->command->info("No JSON files have been published, all of the uri's that the JSON seeder wanted to use are already taken.");
        }
    }

    /**
     * Seed the XLS definitions
     */
    private function seedXls()
    {

        $xls_data = array(
            'baseball' => array(
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
                                )
            ),
        );

        $added = false;

        foreach ($xls_data as $file => $definition_info) {

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'xls')->where('resource_name', '=', $file)->first();

            if (empty($definition)) {

                // Create a new XlsDefinition
                $xls_def = new XlsDefinition();
                $xls_def->uri = app_path() . '/storage/data/xls/' . $file . '.xlsx';
                $xls_def->description = $definition_info['description'];
                $xls_def->sheet = 'Sheet1';
                $xls_def->has_header_row = 1;
                $xls_def->start_row = 0;
                $xls_def->save();

                // Add the tabular columns
                foreach ($definition_info['columns'] as $column) {

                    $tab_column = new TabularColumns();
                    $tab_column->column_name = $column['column_name'];
                    $tab_column->index = $column['index'];
                    $tab_column->column_name_alias = $column['column_name_alias'];
                    $tab_column->is_pk = $column['is_pk'];
                    $tab_column->tabular_id = $xls_def->id;
                    $tab_column->tabular_type = 'XlsDefinition';
                    $tab_column->save();
                }

                // Add the XlsDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = 'xls';
                $definition->resource_name = $file;
                $definition->source_id = $xls_def->id;
                $definition->source_type = 'XlsDefinition';
                $definition->draft = false;
                $definition->save();

                $this->command->info("Published an XLS file, $file, on uri (relative to the root) xls/$file .");
                $added = true;
            }
        }

        if (!$added) {
            $this->command->info("No XLS files have been published, all of the uri's that the XLS seeder wanted to use are already taken.");
        }
    }

    /**
     * Seed the SHP definitions
     */
    private function seedShp()
    {

        $shp_data = array(
            'rivers' => array('file' => 'gis.osm_boundaries_v06',
                                'collection' => 'dresden',
                                'name' => 'rivers',
                                'description' => 'Shape file about rivers in Dresden.',
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
            ),
            'places' => array('file' => 'places',
                              'name' => 'places',
                              'collection' => 'france',
                              'description' => 'Interesting places from "Ile-de-France".',
                              'columns' => array(
                                array(
                                    'column_name' => 'osm_id',
                                    'index' => 0,
                                    'column_name_alias' => 'osm_id',
                                    'is_pk' => 0
                                    ),
                                array(
                                    'column_name' => 'name',
                                    'index' => 1,
                                    'column_name_alias' => 'name',
                                    'is_pk' => 0
                                    ),
                                array(
                                    'column_name' => 'type',
                                    'index' => 2,
                                    'column_name_alias' => 'type',
                                    'is_pk' => 0
                                    ),
                                array(
                                    'column_name' => 'population',
                                    'index' => 3,
                                    'column_name_alias' => 'population',
                                    'is_pk' => 0
                                    ),
                                array(
                                    'column_name' => 'deleted',
                                    'index' => 4,
                                    'column_name_alias' => 'deleted',
                                    'is_pk' => 0
                                    ),
                                array(
                                    'column_name' => 'x',
                                    'index' => 5,
                                    'column_name_alias' => 'x',
                                    'is_pk' => 0
                                    ),
                                array(
                                    'column_name' => 'y',
                                    'index' => 6,
                                    'column_name_alias' => 'y',
                                    'is_pk' => 0
                                    ),
                                ),
                                'geo' => array(
                                            array(
                                                'path' => 'x',
                                                'property' => 'latitude',
                                            ),
                                            array(
                                                'path' => 'y',
                                                'property' => 'longitude',
                                            ),
                                        ),
                                ),
            );

        $added = false;

        foreach ($shp_data as $directory => $info) {

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', $info['collection'])->where('resource_name', '=', $info['name'])->first();

            if (empty($definition)) {

                // Create a new ShpDefinition
                $shp_def = new ShpDefinition();
                $shp_def->uri = app_path() . '/storage/data/shp/' . $directory . '/' . $info['file'] . '.shp';
                $shp_def->description = $info['description'];
                $shp_def->epsg = 4326;
                $shp_def->save();

                // Add the tabular columns
                foreach ($info['columns'] as $column) {

                    $tab_column = new TabularColumns();
                    $tab_column->column_name = $column['column_name'];
                    $tab_column->index = $column['index'];
                    $tab_column->column_name_alias = $column['column_name_alias'];
                    $tab_column->is_pk = $column['is_pk'];
                    $tab_column->tabular_id = $shp_def->id;
                    $tab_column->tabular_type = 'ShpDefinition';
                    $tab_column->save();
                }

                // Add the geo properties
                foreach ($info['geo'] as $geo) {

                    $geo_prop = new GeoProperty();
                    $geo_prop->source_type = 'ShpDefinition';
                    $geo_prop->source_id = $shp_def->id;
                    $geo_prop->property = $geo['property'];
                    $geo_prop->path = $geo['path'];
                    $geo_prop->save();
                }

                // Add the ShpDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = $info['collection'];
                $definition->resource_name = $info['name'];
                $definition->source_id = $shp_def->id;
                $definition->source_type = 'ShpDefinition';
                $definition->draft = false;
                $definition->save();

                $this->command->info("Published a SHP file.");
                $added = true;
            }
        }

        if (!$added) {
            $this->command->info("No SHP files have been published, all of the uri's that the SHP seeder wanted to use are already taken.");
        }
    }
}

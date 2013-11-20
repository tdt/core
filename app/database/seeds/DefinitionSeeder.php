<?php

/**
 * This class seeds the back-end with several definitions with different source types.
 * We use the data from the tests folder as default data sets.
 */

class DefinitionSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        Eloquent::unguard();

        // Add csv definitions
        $this->seedCsv();
        $this->command->info('Succesfully seeded CSV definitions.');

        // Add the json definitions
        $this->seedJson();
        $this->command->info('Succesfully seeded JSON definitions.');

        // Add the xml definitions
        $this->seedXml();
        $this->command->info('Succesfully seeded XML definitions.');

        // Add the shp definitions
        $this->seedShp();
        $this->command->info('Succesfully seeded SHP definitions.');

        // Add the xls definitions
        $this->seedXls();
        $this->command->info('Succesfully seeded XLS definitions.');

    }

    /**
     * Seed the CSV definitions
     */
    private function seedCsv(){

        // The csv file names
        $csv_data = array(
            'comma_in_quotes',
            'escaped_quotes',
            'json',
            'latin1',
            'newlines',
            'quotes_and_newlines',
            'simple',
            'utf8',
            );

        foreach($csv_data as $file){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'csv')->where('resource_name', '=', $file)->first();

            if(empty($definition)){

                // Create a new CsvDefinition
                $csv_def = new CsvDefinition();
                $csv_def->description = "Published CSV file, created from the framework itself. All personal related data, if present, is randomly generated.";
                $csv_def->uri = 'file://' . __DIR__ . '/../../tests/data/csv/' . $file . '.csv';
                $csv_def->delimiter = ',';
                $csv_def->has_header_row = 1;
                $csv_def->start_row = 0;
                $csv_def->save();

                // Add the CsvDefinition to a Definition
                $definition = new Definition();
                $definition->collection_uri = 'csv';
                $definition->resource_name = $file;
                $definition->source_id = $csv_def->id;
                $definition->source_type = 'CsvDefinition';
                $definition->save();
            }
        }
    }

    /**
     * Seed the XML definitions
     */
    private function seedXml(){

        $xml_data = array(
            'persons',
            );

        foreach($xml_data as $file){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'csv')->where('resource_name', '=', $file)->first();

            if(empty($definition)){

                //Create a new XmlDefinition
                $xml_def = new XmlDefinition();
                $xml_def->uri = __DIR__ . '/../../tests/data/xml/' . $file . '.xml';
                $xml_def->description = "Published XML file, created from the framework itself. All personal related data, if present, is randomly generated.";
                $xml_def->save();

                // Add the XmlDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = 'xml';
                $definition->resource_name = $file;
                $definition->source_id = $xml_def->id;
                $definition->source_type = 'XmlDefinition';
                $definition->save();
            }
        }

    }

    /**
     * Seed the JSON definitions
     */
    private function seedJson(){

        // The json file names
        $json_data = array(
            'complex_persons',
            'simple_persons',
            );

        foreach($json_data as $file){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'csv')->where('resource_name', '=', $file)->first();

            if(empty($definition)){

                //Create a new JsonDefinition
                $json_def = new JsonDefinition();
                $json_def->uri = 'file://' . __DIR__ . '/../../tests/data/json/' . $file . '.json';
                $json_def->description = "Published JSON file, created from the framework itself. All personal related data, if present, is randomly generated.";
                $json_def->save();

                // Add the JsonDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = 'json';
                $definition->resource_name = $file;
                $definition->source_id = $json_def->id;
                $definition->source_type = 'JsonDefinition';
                $definition->save();
            }
        }
    }

    /**
     * Seed the XLS definitions
     */
    private function seedXls(){

        $xls_data = array(
            'tabular',
        );

        foreach($xls_data as $file){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'xls')->where('resource_name', '=', $file)->first();

            if(empty($definition)){

                //Create a new XlsDefinition
                $xls_def = new XlsDefinition();
                $xls_def->uri = __DIR__ . '/../../tests/data/xls/' . $file . '.xlsx';
                $xls_def->description = "Published XLS(X) file, created from the framework itself. All personal related data, if present, is randomly generated.";
                $xls_def->sheet = 'Sheet1';
                $xls_def->has_header_row = 1;
                $xls_def->start_row = 0;
                $xls_def->save();

                // Add the XlsDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = 'xls';
                $definition->resource_name = $file;
                $definition->source_id = $xls_def->id;
                $definition->source_type = 'XlsDefinition';
                $definition->save();
            }
        }
    }

    /**
     * Seed the SHP definitions
     */
    private function seedShp(){

        $shp_data = array(
            'boundaries' => 'gis.osm_boundaries_v06',
            );

        foreach($shp_data as $name => $file){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'csv')->where('resource_name', '=', $name)->first();

            if(empty($definition)){

                //Create a new ShpDefinition
                $shp_def = new ShpDefinition();
                $shp_def->uri = __DIR__ . '/../../tests/data/shp/' . $file . '.shp';
                $shp_def->description = "Published SHP file, created from the framework itself. All personal related data, if present, is randomly generated.";
                $shp_def->epsg = 4326;
                $shp_def->save();

                // Add the ShpDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = 'shp';
                $definition->resource_name = $name;
                $definition->source_id = $shp_def->id;
                $definition->source_type = 'ShpDefinition';
                $definition->save();
            }
        }
    }
}

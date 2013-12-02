<?php

/**
 * This class seeds the back-end with several definitions with different source types.
 * We use the data from the tests folder as default data sets.
 */

class TestSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

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

        // Provide a message when nothing has been added (doubles have been found)
        $added = false;

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

                $this->command->info("Published a CSV file on uri (relative to the root) csv/$file .");
                $added = true;
            }
        }

        if(!$added){
            $this->command->info("No CSV files have been published, all of the uri's that the CSV seeder wanted to use are already taken.");
        }
    }

    /**
     * Seed the XML definitions
     */
    private function seedXml(){

        $xml_data = array(
            'persons',
        );

        $added = false;

        foreach($xml_data as $file){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'xml')->where('resource_name', '=', $file)->first();

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

                $this->command->info("Published an XML file on uri (relative to the root) xml/$file .");
                $added = true;
            }
        }

        if(!$added){
            $this->command->info("No XML files have been published, all of the uri's that the XML seeder wanted to use are already taken.");
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

        $added = false;

        foreach($json_data as $file){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'json')->where('resource_name', '=', $file)->first();

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

                $this->command->info("Published a JSON file on uri (relative to the root) json/$file .");
                $added = true;
            }
        }

        if(!$added){
            $this->command->info("No JSON files have been published, all of the uri's that the JSON seeder wanted to use are already taken.");
        }
    }

    /**
     * Seed the XLS definitions
     */
    private function seedXls(){

        $xls_data = array(
            'tabular',
        );

        $added = false;

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

                $this->command->info("Published a XLS file on uri (relative to the root) xls/$file .");
                $added = true;
            }
        }

        if(!$added){
            $this->command->info("No XLS files have been published, all of the uri's that the XLS seeder wanted to use are already taken.");
        }
    }

    /**
     * Seed the SHP definitions
     */
    private function seedShp(){

        $shp_data = array(
            'boundaries' => 'gis.osm_boundaries_v06',
        );

        $added = false;

        foreach($shp_data as $name => $file){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'shp')->where('resource_name', '=', $name)->first();

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

                $this->command->info("Published a SHP file on uri (relative to the root) shp/$file .");
                $added = true;
            }
        }

        if(!$added){
            $this->command->info("No SHP files have been published, all of the uri's that the SHP seeder wanted to use are already taken.");
        }
    }
}

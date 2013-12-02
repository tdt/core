<?php

/**
 * This class seeds the back-end with several definitions with different source types.
 * We use the data from the tests folder as default data sets.
 */

class DemoDataSeeder extends Seeder {

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
            'geo' => 'Geographical data about Afghanistan concerning provinces and districts.',
        );

        // Provide a message when nothing has been added (doubles have been found)
        $added = false;

        foreach($csv_data as $file => $description){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'csv')->where('resource_name', '=', $file)->first();

            if(empty($definition)){

                // Create a new CsvDefinition
                $csv_def = new CsvDefinition();
                $csv_def->description = $description;
                $csv_def->uri = 'file://' . __DIR__ . '/../../storage/data/csv/' . $file . '.csv';
                $csv_def->delimiter = ';';
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

                $this->command->info("Published a CSV file with name $file on uri (relative to the root) csv/$file .");
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
            'persons' => 'Auto-generated xml file about persons.',
        );

        $added = false;

        foreach($xml_data as $file => $description){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'xml')->where('resource_name', '=', $file)->first();

            if(empty($definition)){

                //Create a new XmlDefinition
                $xml_def = new XmlDefinition();
                $xml_def->uri = __DIR__ . '/../../storage/data/xml/' . $file . '.xml';
                $xml_def->description = $description;
                $xml_def->save();

                // Add the XmlDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = 'xml';
                $definition->resource_name = $file;
                $definition->source_id = $xml_def->id;
                $definition->source_type = 'XmlDefinition';
                $definition->save();

                $this->command->info("Published an XML file with file name $file on uri (relative to the root) xml/$file .");
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
            'crime' => 'Crime data from the uk.',
        );

        $added = false;

        foreach($json_data as $file => $description){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'json')->where('resource_name', '=', $file)->first();

            if(empty($definition)){

                //Create a new JsonDefinition
                $json_def = new JsonDefinition();
                $json_def->uri = 'file://' . __DIR__ . '/../../storage/data/json/' . $file . '.json';
                $json_def->description = $description;
                $json_def->save();

                // Add the JsonDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = 'json';
                $definition->resource_name = $file;
                $definition->source_id = $json_def->id;
                $definition->source_type = 'JsonDefinition';
                $definition->save();

                $this->command->info("Published a JSON file, $file, on uri (relative to the root) json/$file .");
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
            'baseball' => 'Individual offensive statistics from the 2008 Major League Baseball season.',
        );

        $added = false;

        foreach($xls_data as $file => $description){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', 'xls')->where('resource_name', '=', $file)->first();

            if(empty($definition)){

                //Create a new XlsDefinition
                $xls_def = new XlsDefinition();
                $xls_def->uri = __DIR__ . '/../../storage/data/xls/' . $file . '.xlsx';
                $xls_def->description = $description;
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

                $this->command->info("Published an XLS file, $file, on uri (relative to the root) xls/$file .");
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
            'rivers' => array('file' => 'gis.osm_boundaries_v06', 'collection' => 'dresden', 'name' => 'rivers', 'description' => 'Shape file about rivers in Dresden.'),
            'places' => array('file' => 'places', 'name' => 'places', 'collection' => 'france', 'description' => 'Interesting places from "Ile-de-France".'),
        );

        $added = false;

        foreach($shp_data as $directory => $info){

            // Don't create doubles
            $definition = Definition::where('collection_uri', '=', $info['collection'])->where('resource_name', '=', $info['name'])->first();

            if(empty($definition)){

                //Create a new ShpDefinition
                $shp_def = new ShpDefinition();
                $shp_def->uri = __DIR__ . '/../../storage/data/shp/' . $directory . '/' . $info['file'] . '.shp';
                $shp_def->description = $info['description'];
                $shp_def->epsg = 4326;
                $shp_def->save();

                // Add the ShpDefinition to the Definition
                $definition = new Definition();
                $definition->collection_uri = $info['collection'];
                $definition->resource_name = $info['name'];
                $definition->source_id = $shp_def->id;
                $definition->source_type = 'ShpDefinition';
                $definition->save();

                $this->command->info("Published a SHP file.");
                $added = true;
            }
        }

        if(!$added){
            $this->command->info("No SHP files have been published, all of the uri's that the SHP seeder wanted to use are already taken.");
        }
    }
}

<?php

use tdt\core\definitions\DefinitionController;
use tdt\core\datasets\DatasetController;
use Symfony\Component\HttpFoundation\Request;

class ShpTest extends TestCase{

    // This array holds the names of the files that can be used
    // to test the shp definitions.
    private $test_data = array(
                array(
                    'name' => 'boundaries',
                    'file' => 'gis.osm_boundaries_v06',
                    'epsg' => 4326
                ),
                /*array(
                    'name' => 'buildings',
                    'file' => 'gis.osm_buildings_v06',
                    'epsg' => 4326,
                ),*/ // These need to wait for the paging implementation, are currently too big to be handled in a testing environment
            );

    public function test_put_api(){

        // Publish each shp file in the test shp data folder.
        foreach($this->test_data as $entry){

            $name = $entry['name'];
            $file = $entry['file'];

            // Set the definition parameters.
            $data = array(
                'description' => "A shp publication from the $file shp file.",
                'epsg' => '4326',
                'uri' => __DIR__ . "/data/shp/$file.shp",
                );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.shp'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $response = DefinitionController::handle("shp/$name");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_get_api(){

        // Request the data for each of the test shp files.
        foreach($this->test_data as $entry){

            $name = $entry['name'];

            $uri = 'shp/'. $name .'.json';
            $this->updateRequest('GET');

            $response = DatasetController::handle($uri);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_delete_api(){

        // Delete the published definition for each test shp file.
        foreach($this->test_data as $entry){

            $name = $entry['name'];

            $this->updateRequest('DELETE');

            $response = DefinitionController::handle("shp/$name");
            $this->assertEquals(200, $response->getStatusCode());
        }

        // Check if everything is deleted properly.
        $definitions_count = Definition::all()->count();
        $shp_count = ShpDefinition::all()->count();

        $this->assertTrue($shp_count == 0);
        $this->assertTrue($definitions_count == 0);
    }
}
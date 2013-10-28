<?php
/**
 * SHP Controller
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Lieven Janssen
 * @author Jan Vansteenlandt
 */

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;

include_once(__DIR__ . "/../../../../lib/ShapeFile.inc.php");
include_once(__DIR__ . "/../../../../lib/proj4php/proj4php.php");

class SHPController extends ADataController {

    public function readData($source_definition, $parameters = null) {

        // It may take a while for the SHP to be read
        set_time_limit(0);

        // Get the limit and offset
        list($limit, $offset) = $this->calculateLimitAndOffset();

        $uri = $source_definition->uri;

        $columns = array();

        $epsg = $source_definition->epsg;

        // The tmp folder of the system, if none is given
        // abort the process
        $tmp_path = sys_get_temp_dir();

        if(empty($tmp_path)){
            \App::abort(452, "The temporary file of the system cannot be found or used.");
        }

        // Fetch the tabular columns of the SHP file
        $columns = $source_definition->tabularColumns()->getResults();

        // Fetch the geo properties of the SHP file
        $geo_props = $source_definition->geoProperties()->getResults();
        $geo = array();

        foreach($geo_props as $geo_prop){
            $geo[$geo_prop->geo_property] = $geo_prop->path;
        }

        if(!$columns){
            \App::abort(452, "Can't find or fetch columns for this SHP file.");
        }

        // Create an array that maps alias names to column names
        $aliases = array();
        foreach($columns as $column){
            $aliases[$column->column_name] = $column->column_name_alias;
        }

        $columns = $aliases;

        try {

            // Create the array in which all the resulting objects will be placed
            $arrayOfRowObjects = array();

            // Prepare the options to read the SHP file
            $options = array('noparts' => false);

            $is_url = (substr($uri , 0, 4) == "http");

            // If the shape files are located on an HTTP address, fetch them and store them locally
            if ($is_url) {

                $tmp_file_name = uniqid();
                $tmp_file = $tmp_path . "/" . $tmp_file_name;

                file_put_contents($tmp_file . ".shp", file_get_contents(substr($uri, 0, strlen($uri) - 4) . ".shp"));
                file_put_contents($tmp_file . ".dbf", file_get_contents(substr($uri, 0, strlen($uri) - 4) . ".dbf"));
                file_put_contents($tmp_file . ".shx", file_get_contents(substr($uri, 0, strlen($uri) - 4) . ".shx"));

                // Along this file the class will use file.shx and file.dbf
                $shp = new \ShapeFile($tmp_file . ".shp", $options);
            } else {

                $shp = new \ShapeFile($uri, $options); // along this file the class will use file.shx and file.dbf
            }

            // Keep track of the total amount of rows
            $total_rows = 0;

            // Get the shape records in the binary file
            while ($record = $shp->getNext()) {

                if($offset <= $total_rows && $offset + $limit > $total_rows){

                    // Every shape record is parsed as an anonymous object with the properties attached to it
                    $rowobject = new \stdClass();

                    // Get the dBASE data
                    $dbf_data = $record->getDbfData();

                    foreach ($dbf_data as $property => $value) {
                        $property = strtolower($property);
                        $rowobject->$property = trim($value);
                    }

                    // Read the shape data
                    $shp_data = $record->getShpData();

                    if (!empty($epsg)) {
                        $proj4 = new \Proj4php();
                        $projSrc = new \Proj4phpProj('EPSG:'. $epsg,$proj4);
                        $projDest = new \Proj4phpProj('EPSG:4326',$proj4);
                    }

                    // It it's not a point, it's a collection of coordinates describing a shape
                    if(!empty($shp_data['parts'])) {

                        $parts = array();

                        foreach ($shp_data['parts'] as $part) {

                            $points = array();

                            foreach ($part['points'] as $point) {

                                $x = $point['x'];
                                $y = $point['y'];

                            // Translate the coordinates to WSG84 geo coordinates
                                if(!empty($epsg)){

                                    $pointSrc = new \proj4phpPoint($x,$y);

                                    $pointDest = $proj4->transform($projSrc,$projDest,$pointSrc);
                                    $x = $pointDest->x;
                                    $y = $pointDest->y;
                                }

                                $points[] = $x.','.$y;
                            }
                            array_push($parts,implode(" ",$points));
                        }

                        $rowobject->parts = implode(';', $parts);
                    }

                    if(isset($shp_data['x'])) {

                        $x = $shp_data['x'];
                        $y = $shp_data['y'];

                        if (!empty($epsg)) {

                            $pointSrc = new \proj4phpPoint($x,$y);
                            $pointDest = $proj4->transform($projSrc,$projDest,$pointSrc);
                            $x = $pointDest->x;
                            $y = $pointDest->y;

                        }

                        $rowobject->x = $x;
                        $rowobject->y= $y;
                    }
                    array_push($arrayOfRowObjects,$rowobject);
                }
                $total_rows++;
            }

            // Calculate the paging headers properties
            $paging = $this->calculatePagingHeaders($limit, $offset, $total_rows);

            $data_result = new Data();
            $data_result->data = $arrayOfRowObjects;
            $data_result->geo = $geo;
            $data_result->paging = $paging;
            return $data_result;

        } catch( Exception $ex) {

            App::abort(452, "Something went wrong while fetching data from the shape file.");
        }
    }
}

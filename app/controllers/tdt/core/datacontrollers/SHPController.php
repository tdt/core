<?php
/**
 * This class handles a SHP file.
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Lieven Janssen
 * @author Jan Vansteenlandt
 */

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;

include_once(__DIR__ . "/../../../../lib/ShapeFile.inc.php");
include_once(__DIR__ . "/../../../../lib/proj4php/proj4php.php");

class SHPController implements IDataController {

    public function readData($source_definition, $parameters = null) {

        // It may take a while for the SHP to be read.
        set_time_limit(1337);

        $uri = $source_definition->uri;

        $columns = array();

        $epsg = $source_definition->epsg;

        // The tmp folder of the system, if none is given
        // abort the process.
        $tmp_path = sys_get_temp_dir();

        if(empty($tmp_path)){
            \App::abort(452, "The temporary file of the system cannot be found or used.");
        }

        // Fetch the columns of the SHP file
        $columns = $source_definition->tabularColumns();
        $columns = $columns->getResults();

        if(!$columns){
            \App::abort(452, "Can't find or fetch columns for this SHP file.");
        }

        // Create an array that maps alias names to column names.
        $aliases = array();
        foreach($columns as $column){
            $aliases[$column->column_name] = $column->column_name_alias;
        }

        $columns = $aliases;

        try {

            // Create the array in which all the resulting objects will be placed.
            $arrayOfRowObjects = array();

            // Prepare the options to read the SHP file.
            $options = array('noparts' => false);

            $isUrl = (substr($uri , 0, 4) == "http");

            // If the shape files are located on an HTTP address, fetch them and store them locally.
            if ($isUrl) {

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

            while ($record = $shp->getNext()) {

                // Every shape record is parsed as an anonymous object with the properties attached to it.
                $rowobject = new \stdClass();
                $dbf_data = $record->getDbfData();

                // Read the meta-data.
                foreach ($dbf_data as $property => $value) {
                    $property = strtolower($property);
                    $rowobject->$property = trim($value);
                }

                // TODO change the hardcoded geo-tags to variable tags. (coords, lat)
                if(in_array("coords",$columns) || in_array("lat",$columns)) {

                    // Read the shape data.
                    $shp_data = $record->getShpData();

                    if (!empty($EPSG)) {
                        $proj4 = new \Proj4php();
                        $projSrc = new \Proj4phpProj('EPSG:'. $EPSG,$proj4);
                        $projDest = new \Proj4phpProj('EPSG:4326',$proj4);
                    }

                    if(isset($shp_data['parts'])) {

                        $parts = array();
                        foreach ($shp_data['parts'] as $part) {
                            $points = array();
                            foreach ($part['points'] as $point) {

                                $x = $point['x'];
                                $y = $point['y'];

                                // Translate the coordinates to understandable geo coordinates.
                                if(!empty($EPSG)){

                                    $pointSrc = new \proj4phpPoint($x,$y);

                                    $pointDest = $proj4->transform($projSrc,$projDest,$pointSrc);
                                    $x = $pointDest->x;
                                    $y = $pointDest->y;
                                }

                                $points[] = $x.','.$y;
                            }
                            array_push($parts,implode(" ",$points));
                        }

                        $rowobject->coords = implode(';', $parts);
                    }

                    if(isset($shp_data['x'])) {

                        $x = $shp_data['x'];
                        $y = $shp_data['y'];

                        if (!empty($EPSG)) {

                            $pointSrc = new \proj4phpPoint($x,$y);
                            $pointDest = $proj4->transform($projSrc,$projDest,$pointSrc);
                            $x = $pointDest->x;
                            $y = $pointDest->y;

                        }

                        $rowobject->long = $x;
                        $rowobject->lat = $y;
                    }
                }

                array_push($arrayOfRowObjects,$rowobject);
            }

            $data_result = new Data();
            $data_result->data = $arrayOfRowObjects;
            return $data_result;

        } catch( Exception $ex) {

            App::abort(452, "Something went wrong while fetching data from the shape file.");
        }
    }
}
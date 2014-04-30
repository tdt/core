<?php
/**
 * SHP Controller
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Lieven Janssen
 * @author Jan Vansteenlandt
 */

namespace Tdt\Core\DataControllers;

use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface;

include_once(app_path() . "/lib/ShapeFile.inc.php");
include_once(app_path() . "/lib/proj4php/proj4php.php");

class SHPController extends ADataController
{

    private $tabular_columns;
    private $geo_property;

    public function __construct(TabularColumnsRepositoryInterface $tabular_columns, GeoPropertyRepositoryInterface $geo_property)
    {
        $this->tabular_columns = $tabular_columns;
        $this->geo_property = $geo_property;
    }

    public function readData($source_definition, $rest_parameters = array())
    {
        // It may take a while for the SHP to be read
        set_time_limit(0);

        // Get the limit and offset
        list($limit, $offset) = Pager::calculateLimitAndOffset();

        // Disregard the paging when rest parameters are given
        if (!empty($rest_parameters)) {
            $limit = PHP_INT_MAX;
            $offset = 0;
        }

        $uri = $source_definition['uri'];

        $columns = array();

        $epsg = $source_definition['epsg'];

        // The tmp folder of the system, if none is given
        // abort the process
        $tmp_path = sys_get_temp_dir();

        if (empty($tmp_path)) {
            // If this occurs then the server is not configured correctly, thus a 500 error is thrown
            \App::abort(500, "The temp directory, retrieved by the operating system, could not be retrieved.");
        }

        // Get the columns
        $columns = $this->tabular_columns->getColumnAliases($source_definition['id'], 'ShpDefinition');

        // Get the geo properties
        $geo_properties = $this->geo_property->getGeoProperties($source_definition['id'], 'ShpDefinition');

        $geo = array();

        foreach ($geo_properties as $geo_prop) {
            $geo[$geo_prop['property']] = $geo_prop['path'];
        }

        if (!$columns) {
            \App::abort(500, "Cannot find the columns of the SHP definition.");
        }

        try {

            // Create the array in which all the resulting objects will be placed
            $arrayOfRowObjects = array();

            // Prepare the options to read the SHP file
            $options = array('noparts' => false);

            $is_url = (substr($uri, 0, 4) == "http");

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

                if ($offset <= $total_rows && $offset + $limit > $total_rows) {

                    // Every shape record is parsed as an anonymous object with the properties attached to it
                    $rowobject = new \stdClass();

                    // Get the dBASE data
                    $dbf_data = $record->getDbfData();

                    foreach ($dbf_data as $property => $value) {
                        $property = strtolower($property);
                        $property_alias = $columns[$property];
                        $rowobject->$property_alias = trim($value);
                    }

                    // Read the shape data
                    $shp_data = $record->getShpData();

                    if (!empty($epsg)) {
                        $proj4 = new \Proj4php();
                        $projSrc = new \Proj4phpProj('EPSG:'. $epsg, $proj4);
                        $projDest = new \Proj4phpProj('EPSG:4326', $proj4);
                    }

                    // It it's not a point, it's a collection of coordinates describing a shape
                    if (!empty($shp_data['parts'])) {

                        $parts = array();

                        foreach ($shp_data['parts'] as $part) {

                            $points = array();

                            foreach ($part['points'] as $point) {

                                $x = $point['x'];
                                $y = $point['y'];

                                // Translate the coordinates to WSG84 geo coordinates
                                if (!empty($epsg)) {

                                    $pointSrc = new \proj4phpPoint($x, $y);

                                    $pointDest = $proj4->transform($projSrc, $projDest, $pointSrc);
                                    $x = $pointDest->x;
                                    $y = $pointDest->y;
                                }

                                $points[] = $x.','.$y;
                            }
                            array_push($parts, implode(" ", $points));
                        }

                        // Parts only contains 1 shape, thus 1 geo entry
                        $alias = reset($geo);

                        $rowobject->$alias = implode(';', $parts);
                    }

                    if (isset($shp_data['x'])) {

                        $x = $shp_data['x'];
                        $y = $shp_data['y'];

                        if (!empty($epsg)) {

                            $pointSrc = new \proj4phpPoint($x, $y);
                            $pointDest = $proj4->transform($projSrc, $projDest, $pointSrc);
                            $x = $pointDest->x;
                            $y = $pointDest->y;

                        }

                        $rowobject->$geo['longitude'] = $x;
                        $rowobject->$geo['latitude'] = $y;
                    }
                    array_push($arrayOfRowObjects, $rowobject);
                }
                $total_rows++;
            }

            // Calculate the paging headers properties
            $paging = Pager::calculatePagingHeaders($limit, $offset, $total_rows);

            $data_result = new Data();
            $data_result->data = $arrayOfRowObjects;
            $data_result->geo = $geo;
            $data_result->paging = $paging;
            $data_result->preferred_formats = array('map');

            return $data_result;

        } catch (Exception $ex) {

            \App::abort(500, "Something went wrong while putting the SHP files in a temporary directory or during the extraction of the SHP data. The error message is: $ex->getMessage().");
        }
    }

    /**
     * Parse the column names out of a SHP file
     */
    public static function parseColumns($options)
    {
        $is_url = (substr($options['uri'], 0, 4) == "http");
        $tmp_dir = sys_get_temp_dir();
        $columns = array();

        $pk = @$options['pk'];

        try {
            if ($is_url) {

                // This remains untested
                $tmp_file = uniqid();
                file_put_contents($tmp_dir . '/' . $tmp_file . ".shp", file_get_contents(substr($options['uri'], 0, strlen($options['uri']) - 4) . ".shp"));
                file_put_contents($tmp_dir . '/' . $tmp_file . ".dbf", file_get_contents(substr($options['uri'], 0, strlen($options['uri']) - 4) . ".dbf"));
                file_put_contents($tmp_dir . '/' . $tmp_file . ".shx", file_get_contents(substr($options['uri'], 0, strlen($options['uri']) - 4) . ".shx"));

                // Along this file the class will use file.shx and file.dbf
                $shp = new \ShapeFile($tmp_dir . '/' . $tmp_file . ".shp", array('noparts' => false));
            } else {

               // along this file the class will use file.shx and file.dbf
                $shp = new \ShapeFile($options['uri'], array('noparts' => false));
            }
        } catch (Exception $e) {
            \App::abort(400, "The shape contents couldn't be retrieved, make sure the shape file is valid, zipped shape files are not yet supported.");
        }

        $record = $shp->getNext();

        // Read meta data
        if (!$record) {
            $uri = $options['uri'];
            \App::abort(400, "We failed to retrieve a record from the provided shape file on uri $uri, make sure the corresponding dbf and shx files are at the same location.");
        }

        // Get the dBASE fields
        $dbf_fields = $record->getDbfFields();
        $column_index = 0;

        foreach ($dbf_fields as $field) {

            $property = strtolower($field["fieldname"]);
            array_push($columns, array('index' => $column_index, 'column_name' => $property, 'column_name_alias' => $property, 'is_pk' => ($pk === $column_index)));
            $column_index++;
        }

        $shp_data = $record->getShpData();

        // Get the geographical column names
        // Either coords will be set (identified by the parts)
        // or a lat long will be set (identified by x and y)
        if (!empty($shp_data['parts'])) {
            array_push($columns, array('index' => $column_index, 'column_name' => 'parts', 'column_name_alias' => 'parts', 'is_pk' => 0));
        } elseif (!empty($shp_data['x'])) {
            array_push($columns, array('index' => $column_index, 'column_name' => 'x', 'column_name_alias' => 'x', 'is_pk' => 0));
            array_push($columns, array('index' => $column_index + 1, 'column_name' => 'y', 'column_name_alias' => 'y', 'is_pk' => 0));
        } else {
            \App::abort(400, 'The shapefile could not be processed, probably because the geometry in the shape file is not supported. The supported geometries are Null Shape, Point, PolyLine, Polygon and MultiPoint');
        }

        return $columns;
    }


    /**
     * Parse the geo column names out of a SHP file.
     */
    public static function parseGeoProperty($options, $columns)
    {
        // Make sure the geo property's path is mapped onto the column alias
        $aliases = array();

        foreach ($columns as $column) {
            $aliases[$column['column_name']] = $column['column_name_alias'];
        }

        $is_url = (substr($options['uri'], 0, 4) == "http");
        $tmp_dir = sys_get_temp_dir();
        $geo_properties = array();

        if ($is_url) {

            // This remains untested
            $tmp_file = uniqid();
            file_put_contents($tmp_dir . '/' . $tmp_file . ".shp", file_get_contents(substr($options['uri'], 0, strlen($options['uri']) - 4) . ".shp"));
            file_put_contents($tmp_dir . '/' . $tmp_file . ".dbf", file_get_contents(substr($options['uri'], 0, strlen($options['uri']) - 4) . ".dbf"));
            file_put_contents($tmp_dir . '/' . $tmp_file . ".shx", file_get_contents(substr($options['uri'], 0, strlen($options['uri']) - 4) . ".shx"));

            $shp = new \ShapeFile($tmp_dir . '/' . $tmp_file . ".shp", array('noparts' => false));
        } else {
            $shp = new \ShapeFile($options['uri'], array('noparts' => false));
        }

        $record = $shp->getNext();

        // read meta data
        if (!$record) {
            $uri = $options['uri'];
            \App::abort(400, "We failed to retrieve a record from the provided shape file on uri $uri, make sure the corresponding dbf and shx files are at the same location.");
        }

        $shp_data = $record->getShpData();
        $shape_type = strtolower($record->getRecordClass());

        $geo_properties = array();

        // Get the geographical column names
        // Either multiple coordinates will be set (identified by the parts)
        // or a lat long pair will be set (identified by x and y)
        if (!empty($shp_data['parts'])) {
            if (strpos($shape_type, 'polyline')) {
                $parts = $aliases['parts'];
                array_push($geo_properties, array('property' => 'polyline', 'path' => $parts));
            } elseif (strpos($shape_type, 'polygon')) {
                $parts = $aliases['parts'];
                array_push($geo_properties, array('property' => 'polygon', 'path' => $parts));
            } else { // TODO support more types
                \App::abort(400, 'Provided geometric type ($shape_type) is not supported');
            }
        } elseif (isset($shp_data['x'])) {
            $x = $aliases['x'];
            $y = $aliases['y'];
            array_push($geo_properties, array('property' => 'latitude', 'path' => $x));
            array_push($geo_properties, array('property' => 'longitude', 'path' => $y));
        }

        return $geo_properties;
    }
}

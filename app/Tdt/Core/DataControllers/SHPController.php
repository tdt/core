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

use proj4php\Proj;
use proj4php\Proj4php;
use proj4php\Point;
use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface;
use muka\ShapeReader\ShapeReader;
use Tdt\Core\Repositories\Interfaces\GeoprojectionRepositoryInterface;

class SHPController extends ADataController
{

    private $tabular_columns;
    private $geo_property;

    private static $RECORD_TYPES = [
        0 => "Null Shape",
        1 => "Point",
        3 => "PolyLine",
        5 => "Polygon",
        8 => "MultiPoint",
        11 => "PointZ",
        13 => "PolyLineZ",
        15 => "PolygonZ",
        18 => "MultiPointZ"
    ];

    public function __construct(
        TabularColumnsRepositoryInterface $tabular_columns,
        GeoPropertyRepositoryInterface $geo_property,
        GeoprojectionRepositoryInterface $projections
    ) {
        $this->tabular_columns = $tabular_columns;
        $this->geo_property = $geo_property;
        $this->projections = $projections;
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

        $this->epsg = $source_definition['epsg'];

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
                $shp = new ShapeReader($tmp_file . ".shp", $options);
            } else {
                $shp = new ShapeReader($uri, $options); // along this file the class will use file.shx and file.dbf
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
                        $property_alias = $columns[$property];
                        $property = trim($property);
                        $property_alias = $columns[$property];
                        $rowobject->$property_alias = trim($value);
                    }

                    // Read the shape data
                    $shp_data = $record->getShpData();

                    $shape_type = self::$RECORD_TYPES[$record->getTypeCode()];

                    // Get the projection code
                    $projection = $this->projections->getByCode($this->epsg);
                    $projCode = $projection['projection'];

                    if (empty($projCode)) {
                        \App::abort(400, "Could not find a supported EPSG code.");
                    }

                    $this->proj4 = new Proj4php();

                    $this->projSrc = new Proj('EPSG:' . $this->epsg, $this->proj4);
                    $this->projDest = new Proj('EPSG:4326', $this->proj4);

                    $geometry = [];

                    switch (strtolower($shape_type)) {
                        case 'point':
                            $point = $this->parsePoint($shp_data);

                            $rowobject->x = $point['x'];
                            $rowobject->y = $point['y'];
                            break;
                        case 'polyline':
                            $rowobject->parts = $this->parsePolyline($shp_data);
                            break;
                        case 'polygon':
                            $rowobject->parts = $this->parsePolygon($shp_data);
                            break;
                        case 'multipoint':
                            $rowobject->points = $this->parseMultipoint($shp_data);
                            break;
                        case 'pointz':
                            $point = $this->parsePointZ($shp_data);

                            $rowobject->x = $point['x'];
                            $rowobject->y = $point['y'];
                            $rowobject->z = $point['z'];
                            break;
                        case 'polylinez':
                            $rowobject->parts = $this->parsePolylineZ($shp_data);
                            break;
                        case 'polygonz':
                            $rowobject->parts = $this->parsePolygonZ($shp_data);
                            break;
                        case 'multipointz':
                            $rowobject->points = $this->parseMultiPointZ($shp_data);
                            break;
                    }

                    array_push($arrayOfRowObjects, $rowobject);
                }

                $total_rows++;

                if ($total_rows >= 10000) {
                    break;
                }
            }

            // Calculate the paging headers properties
            $paging = Pager::calculatePagingHeaders($limit, $offset, $total_rows);

            $data_result = new Data();
            $data_result->data = $arrayOfRowObjects;
            $data_result->geo = $geo;
            $data_result->paging = $paging;
            $data_result->preferred_formats = array('map', 'geojson');

            return $data_result;
        } catch (Exception $ex) {
            \App::abort(500, "Something went wrong while putting the SHP files in a temporary directory or during the extraction of the SHP data. The error message is: $ex->getMessage().");
        }
    }

    private function parsePoint($shp_data)
    {
        // x = long, y = lat
        $x = $shp_data['x'];
        $y = $shp_data['y'];

        if (!empty($x) && !empty($y)) {
            if (!empty($this->epsg) && $this->epsg != 4326) {
                $pointSrc = new Point($x, $y);

                $pointDest = $this->proj4->transform($this->projSrc, $this->projDest, $pointSrc);
                $x = $pointDest->x;
                $y = $pointDest->y;
            }

            $geo['x'] = $x;
            $geo['y'] = $y;
        }

        return $geo;
    }

    private function parsePointZ($shp_data)
    {
        // x = long, y = lat
        $x = $shp_data['x'];
        $y = $shp_data['y'];
        $z = $shp_data['z'];

        if (!empty($x) && !empty($y) && !empty($z)) {
            if (!empty($this->epsg) && $this->epsg != 4326) {
                $pointSrc = new Point($x, $y, $z);

                $pointDest = $this->proj4->transform($this->projSrc, $this->projDest, $pointSrc);
                $x = $pointDest->x;
                $y = $pointDest->y;
                $z = $pointDest->z;
            }

            $geo['x'] = $x;
            $geo['y'] = $y;
            $geo['z'] = $z;
        }

        return $geo;
    }

    private function parsePolyline($shp_data)
    {
        $parts = array();

        foreach ($shp_data['parts'] as $part) {
            $points = array();

            foreach ($part['points'] as $point) {
                $x = $point['x'];
                $y = $point['y'];

                // Translate the coordinates to WSG84 geo coordinates
                if (!empty($this->epsg)) {
                    $pointSrc = new Point($x, $y);

                    $pointDest = $this->proj4->transform($this->projSrc, $this->projDest, $pointSrc);
                    $x = $pointDest->x;
                    $y = $pointDest->y;
                }

                $points[] = $x . ',' . $y;
            }
            array_push($parts, implode(" ", $points));
        }

        return implode(';', $parts);
    }

    private function parsePolylineZ($shp_data)
    {
        $parts = array();

        foreach ($shp_data['parts'] as $part) {
            $points = array();

            foreach ($part['points'] as $point) {
                $x = $point['x'];
                $y = $point['y'];
                $z = $point['z'];

                // Translate the coordinates to WSG84 geo coordinates
                if (!empty($this->epsg)) {
                    $pointSrc = new Point($x, $y, $z);

                    $pointDest = $this->proj4->transform($this->projSrc, $this->projDest, $pointSrc);
                    $x = $pointDest->x;
                    $y = $pointDest->y;
                }

                $points[] = $x . ',' . $y . ',' . $z;
            }
            array_push($parts, implode(" ", $points));
        }

        return implode(';', $parts);
    }

    private function parsePolygon($shp_data)
    {
        $parts = array();

        foreach ($shp_data['parts'] as $part) {
            $points = array();

            foreach ($part['points'] as $point) {
                $x = $point['x'];
                $y = $point['y'];

                // Translate the coordinates to WSG84 geo coordinates
                if (!empty($this->epsg)) {
                    $pointSrc = new Point($x, $y);

                    $pointDest = $this->proj4->transform($this->projSrc, $this->projDest, $pointSrc);
                    $x = $pointDest->x;
                    $y = $pointDest->y;
                }

                $points[] = $x . ',' . $y;
            }
            array_push($parts, implode(" ", $points));
        }

        return $parts = implode(';', $parts);
    }

    private function parsePolygonZ($shp_data)
    {
        $parts = array();

        foreach ($shp_data['parts'] as $part) {
            $points = array();

            foreach ($part['points'] as $point) {
                $x = $point['x'];
                $y = $point['y'];
                $z = $point['z'];

                // Translate the coordinates to WSG84 geo coordinates
                if (!empty($this->epsg)) {
                    $pointSrc = new Point($x, $y, $z);

                    $pointDest = $this->proj4->transform($this->projSrc, $this->projDest, $pointSrc);
                    $x = $pointDest->x;
                    $y = $pointDest->y;
                    $z = $pointDest->z;
                }

                $points[] = $x . ',' . $y . ',' . $z;
            }
            array_push($parts, implode(" ", $points));
        }

        return $parts = implode(';', $parts);
    }

    private function parseMultipoint($shp_data)
    {
        foreach ($shp_data['points'] as $point) {
            $x = $point['x'];
            $y = $point['y'];

            if (!empty($x) && !empty($y)) {
                if (!empty($this->epsg)) {
                    $pointSrc = new Point($x, $y);

                    $pointDest = $this->proj4->transform($this->projSrc, $this->projDest, $pointSrc);

                    $x = $pointDest->x;
                    $y = $pointDest->y;
                }

                $points[] = $x . ',' . $y;
            }
        }

        return implode(';', $points);
    }

    private function parseMultipointZ($shp_data)
    {
        foreach ($shp_data['points'] as $point) {
            $x = $point['x'];
            $y = $point['y'];
            $z = $point['z'];

            if (!empty($x) && !empty($y) && !empty($z)) {
                if (!empty($this->epsg)) {
                    $pointSrc = new Point($x, $y, $z);

                    $pointDest = $this->proj4->transform($this->projSrc, $this->projDest, $pointSrc);

                    $x = $pointDest->x;
                    $y = $pointDest->y;
                    $z = $pointDest->z;
                }

                $points[] = $x . ',' . $y . ',' . $z;
            }
        }

        return implode(';', $points);
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
                $shp = new ShapeReader($tmp_dir . '/' . $tmp_file . ".shp", array('noparts' => false));
            } else {
               // along this file the class will use file.shx and file.dbf
                $shp = new ShapeReader($options['uri'], array('noparts' => false));
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
        $dbf_fields = $record->getDbfData();

        $column_index = 0;

        foreach ($dbf_fields as $field => $value) {
            // Remove non-printable characters
            $property = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $field);

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
        } elseif (!empty($shp_data['points'])) {
            array_push($columns, array('index' => $column_index, 'column_name' => 'points', 'column_name_alias' => 'points', 'is_pk' => 0));
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

            $shp = new ShapeReader($tmp_dir . '/' . $tmp_file . ".shp", array('noparts' => false));
        } else {
            $shp = new ShapeReader($options['uri'], array('noparts' => false));
        }

        $record = $shp->getNext();

        // read meta data
        if (!$record) {
            $uri = $options['uri'];
            \App::abort(400, "We failed to retrieve a record from the provided shape file on uri $uri, make sure the corresponding dbf and shx files are at the same location.");
        }

        $shp_data = $record->getShpData();
        $shape_type = strtolower($record->getTypeLabel());

        $geo_properties = array();


        // Get the geographical column names
        // Either multiple coordinates will be set (identified by the parts)
        // or a lat long pair will be set (identified by x and y)
        $shp_data = $record->getShpData();

        $shape_type = self::$RECORD_TYPES[$record->getTypeCode()];

        switch (strtolower($shape_type)) {
            case 'point':
                $x = $aliases['x'];
                $y = $aliases['y'];

                array_push($geo_properties, array('property' => 'latitude', 'path' => $x));
                array_push($geo_properties, array('property' => 'longitude', 'path' => $y));
                break;
            case 'pointz':
                $x = $aliases['x'];
                $y = $aliases['y'];
                $z = $aliases['z'];

                array_push($geo_properties, array('property' => 'latitude', 'path' => $x));
                array_push($geo_properties, array('property' => 'longitude', 'path' => $y));
                array_push($geo_properties, array('property' => 'elevation', 'path' => $z));
                break;
            case 'polyline':
                $parts = $aliases['parts'];
                array_push($geo_properties, array('property' => 'polyline', 'path' => $parts));
                break;
            case 'polylinez':
                $parts = $aliases['parts'];
                array_push($geo_properties, array('property' => 'polylinez', 'path' => $parts));
                break;
            case 'polygon':
                $parts = $aliases['parts'];
                array_push($geo_properties, array('property' => 'polygon', 'path' => $parts));
                break;
            case 'polygonz':
                $parts = $aliases['parts'];
                array_push($geo_properties, array('property' => 'polygonz', 'path' => $parts));
                break;
            case 'multipoint':
                $parts = $aliases['points'];
                array_push($geo_properties, array('property' => 'multipoint', 'path' => $parts));
                break;
            case 'multipointz':
                $parts = $aliases['points'];
                array_push($geo_properties, array('property' => 'multipointz', 'path' => $parts));
                break;
        }

        return $geo_properties;
    }
}

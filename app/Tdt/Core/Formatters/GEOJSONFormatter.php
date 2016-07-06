<?php

namespace Tdt\Core\Formatters;

use Tdt\Core\Formatters\XMLFormatter;
use Symm\Gisconverter\Gisconverter;

/**
 * GeoJSON Formatter
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Dieter De Paepe
 */
class GEOJSONFormatter implements IFormatter
{
    public static function createResponse($dataObj)
    {
        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'application/vnd.geo+json');

        return $response;
    }

    public static function getBody($dataObj)
    {
        // Check if the original data is not GeoJSON
        if (!empty($dataObj->geo_formatted) && $dataObj->geo_formatted) {
            if ($dataObj->source_definition['type'] == 'JSON') {
                return json_encode($dataObj->data);
            } elseif ($dataObj->source_definition['type'] == 'KML') {
                $geom = \geoPHP::load($dataObj->data, 'kml');
                return $geom->out('json');
            }
        }

        // Build the body
        $body = $dataObj->data;
        if (is_object($body)) {
            $body = get_object_vars($dataObj->data);
        }

        $features = array();

        foreach ($body as $dataRow) {
            if (is_object($dataRow)) {
                $dataRow = get_object_vars($dataRow);
            }

            $geo = $dataObj->geo;

            //Guess lat/lon if no geo information was given for this
            if (empty($geo)) {
                if ($lat_long = GeoHelper::findLatLong($dataRow)) {
                    $geo = array(
                        "latitude" => $lat_long[0],
                        "longitude" => $lat_long[1]);
                }
            }

            $geometric_ids = self::findGeometry($geo, $dataRow);

            //Prevent geo information being duplicated in properties
            foreach ($geometric_ids[0] as $geometric_id) {
                unset($dataRow[$geometric_id]);
            }

            $feature = array(
                'type' => 'Feature',
                'geometry' => $geometric_ids[1],
                'properties' => $dataRow
            );

            $id_prop = @$dataObj->source_definition['map_property'];

            if (!empty($id_prop) && !empty($dataRow[$id_prop])) {
                $feature['id'] = $dataRow[$id_prop];
                unset($dataRow[$id_prop]);
            }
            array_push($features, $feature);
        }

        $result = array(
            'type' => 'FeatureCollection',
            'features' => $features);

        // Only add bounding box if we have features and are viewing the entire dataset.
        if (!empty($features) && empty($dataObj->paging)) {
            $result['bbox'] = self::boundingBox($features);
        }


        return json_encode($result);
    }

    /**
     * @param array $geo     an array holding the identifier(s) for the geographical attribute
     * @param array $dataRow an array holding data
     * @return array         an array containing the identifier(s) of the selected attribute and an
     * object representing the extracted geometry
     */
    public static function findGeometry($geo, $dataRow)
    {
        $geometry = null;
        $identifiers = array();

        if (count($geo) == 2 && !empty($geo['longitude']) && !empty($geo['latitude'])) {
            array_push($identifiers, $geo['longitude']);
            array_push($identifiers, $geo['latitude']);
            $geometry = array(
                'type' => 'Point',
                'coordinates' => array(
                    floatval($dataRow[$geo['longitude']]),
                    floatval($dataRow[$geo['latitude']]))
            );
        } elseif (count($geo) == 3 && !empty($geo['longitude']) && !empty($geo['latitude']) && !empty($geo['elevation'])) {
            array_push($identifiers, $geo['longitude']);
            array_push($identifiers, $geo['latitude']);
            array_push($identifiers, $geo['elevation']);

            $geometry = array(
                'type' => 'Point',
                'coordinates' => array(
                    floatval($dataRow[$geo['longitude']]),
                    floatval($dataRow[$geo['latitude']]),
                    floatval($dataRow[$geo['elevation']]))
            );
        } else {
            $geo_type = key($geo);

            if (!empty($geo_type)) {
                switch ($geo_type) {
                    case 'point':
                        array_push($identifiers, $geo['point']);
                        $coords = explode(',', $dataRow[$geo['point']]);
                        $geometry = array(
                            'type' => 'Point',
                            'coordinates' => array($coords[0], $coords[1])
                            );
                        break;
                    case 'polyline':
                        array_push($identifiers, $geo['polyline']);
                        $geometry = array(
                            'type' => 'MultiLineString',
                            'coordinates' => self::convertCoordinateMultiArray($dataRow[$geo['polyline']])
                            );
                        break;
                    case 'polygon':
                        array_push($identifiers, $geo['polygon']);
                        $geometry = array(
                            'type' => 'Polygon',
                            'coordinates' => self::convertCoordinateMultiArray($dataRow[$geo['polygon']])
                            );
                        break;
                    case 'multipoint':
                        array_push($identifiers, $geo['multipoint']);
                        $geometry = array(
                            'type' => 'MultiPoint',
                            'coordinates' => self::convertCoordinateSingleArray($dataRow[$geo['multipoint']])
                            );
                        break;
                    case 'pointz':
                        array_push($identifiers, $geo['pointz']);
                        $coords = explode(',', $dataRow[$geo['pointz']]);
                        $geometry = array(
                            'type' => 'Point',
                            'coordinates' => array($coords[0], $coords[1], $coords[2])
                            );
                        break;
                    case 'polylinez':
                        array_push($identifiers, $geo['polylinez']);
                        $geometry = array(
                            'type' => 'MultiLineString',
                            'coordinates' => self::convertCoordinateMultiArray($dataRow[$geo['polylinez']])
                            );
                        break;
                    case 'polygonz':
                        array_push($identifiers, $geo['polygonz']);
                        $geometry = array(
                            'type' => 'Polygon',
                            'coordinates' => self::convertCoordinateMultiArray($dataRow[$geo['polygonz']])
                            );
                        break;
                    case 'multipointz':
                        array_push($identifiers, $geo['multipoint']);
                        $geometry = array(
                            'type' => 'MultiPoint',
                            'coordinates' => self::convertCoordinateSingleArray($dataRow[$geo['multipointz']])
                            );
                        break;
                }
            }
        }

        return array($identifiers, $geometry);
    }

    /**
     * Returns an array of n*2 numbers, where n is the maximum dimension of each
     * coordinate present in the dataset. The first n numbers are the lower bounds of each
     * dimension, the latter n numbers are the upper bounds of each dimension.
     * @param $features array features for which to calculate the bounds
     * @return array
     */
    public static function boundingBox($features)
    {
        //The maximum dimensionality shared by all features (most likely 2 or 3).
        // E.g.: Some features may be 2D, some may be 3D, in which case this value
        // will be set to 2.
        $max_dimension = 100;
        $lowbounds = array();
        $topbounds = array();
        foreach ($features as $feature) {
            if (empty($feature['geometry'])) {
                // Geometry can be null.
                continue;
            }

            $coordinateList = array();
            self::toCoordinateList($feature['geometry']['coordinates'], $coordinateList);

            foreach ($coordinateList as $coordinate) {
                $max_dimension = min($max_dimension, count($coordinate));
                for ($dim = 0; $dim < $max_dimension; $dim += 1) {
                    if (empty($lowbounds[$dim])) {
                        // First coordinate encountered
                        $lowbounds[$dim] = $coordinate[$dim];
                        $topbounds[$dim] = $coordinate[$dim];
                    } else {
                        $lowbounds[$dim] = min($lowbounds[$dim], $coordinate[$dim]);
                        $topbounds[$dim] = max($topbounds[$dim], $coordinate[$dim]);
                    }
                }
            }
        }
        return array_merge(
            array_slice($lowbounds, 0, $max_dimension),
            array_slice($topbounds, 0, $max_dimension)
        );
    }

    /**
     * Converts a coordinate array of a feature of any type to an array containing
     * all used coordinates. Each coordinate is represented by an array of numbers, with one
     * number for each dimension.
     * @param $coordinates_array
     * @param $out array eg for 2D data: ((1, 1), (2, 2), (3, 3))
     */
    public static function toCoordinateList($coordinates_array, &$out)
    {
        if (!empty($coordinates_array) && !is_array($coordinates_array[0])) {
            array_push($out, $coordinates_array);
            return;
        }

        foreach ($coordinates_array as $array) {
            if (empty($array)) {
                continue;
            }
            if (is_array($array[0])) {
                self::toCoordinateList($array, $out);
            } else {
                array_push($out, $array);
            }
        }
    }

    /**
     * @param $str string eg: "1.1,2.2 3.3,4.4"
     * @return array eg: ((1.1, 2.2), (3.3, 4.4))
     */
    public static function convertCoordinateArray($str)
    {
        $result = array();
        foreach (explode(' ', $str) as $coordinate_str) {
            $coord_array = explode(',', $coordinate_str);

            if (count($coord_array) == 2) {
                array_push($result, array(floatval($coord_array[0]), floatval($coord_array[1])));
            } elseif (count($coord_array) == 3) {
                array_push($result, array(floatval($coord_array[0]), floatval($coord_array[1]), floatval($coord_array[2])));
            } else {
                \Log::warning("An invalid coordinate was parsed.");
            }
        }
        return $result;
    }

    /**
     * @param $str string eg: "1.1,2.2 3.3,4.4; 5.5,6.6 7.7,8.8"
     * @return array eg: (((1.1, 2.2), (3.3, 4.4)), ((5.5, 6.6), (7.7, 8.8))
     */
    public static function convertCoordinateMultiArray($str)
    {
        $result = array();
        foreach (explode(';', $str) as $coord_str) {
            array_push($result, self::convertCoordinateArray($coord_str));
        }
        return $result;
    }

    /**
     * @param $str string eg: "1.1,2.2; 3.3,4.4; 5.5,6.6"
     * @return array eg: ((1.1, 2.2), (3.3, 4.4), (5.5, 6.6), (7.7, 8.8))
     */
    public static function convertCoordinateSingleArray($str)
    {
        $result = array();
        foreach (explode(';', $str) as $coord_str) {
            $coordinates_array = explode(',', $coord_str);

            if (count($coordinates_array) == 2) {
                array_push($result, array(floatval($coordinates_array[0]), floatval($coordinates_array[1])));
            } elseif (count($coordinates_array) == 3) {
                array_push($result, array(floatval($coordinates_array[0]), floatval($coordinates_array[1]), floatval($coordinates_array[2])));
            } else {
                \Log::warning("An invalid coordinate was parsed.");
            }
        }
        return $result;
    }

    public static function getDocumentation()
    {
        return "Returns a GeoJSON document.";
    }
}

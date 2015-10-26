<?php

namespace Tdt\Core\Formatters;

/**
 * GEOJson Formatter
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

            $geomIDs_geom = self::findGeometry($geo, $dataRow);

            //Prevent geo information being duplicated in properties
            foreach ($geomIDs_geom[0] as $geomID) {
                unset($dataRow[$geomID]);
            }

            $feature = array(
                'type' => 'Feature',
                'geometry' => $geomIDs_geom[1],
                'properties' => $dataRow
            );
            if (!empty($id_prop = $dataObj->source_definition['map_property']) && !empty($dataRow[$id_prop])) {
                $feature['id'] = $dataRow[$id_prop];
                unset($dataRow[$id_prop]);
            }
            array_push($features, $feature);
        }

        $result = array(
            'type' => 'FeatureCollection',
            'features' => $features);

        return json_encode($result);
    }

    /**
     * @param $geo array an array holding the identifier(s) for the geographical attribute
     * @param $dataRow array an array holding attributes
     * @return array an array containing the identifier(s) of the selected attribute and an
     * object representing the extracted geometry
     */
    public static function findGeometry($geo, $dataRow)
    {
        $geometry = null;
        $identifiers = array();
        if (!empty($geo['longitude']) && !empty($geo['latitude'])) {
            array_push($identifiers, $geo['longitude']);
            array_push($identifiers, $geo['latitude']);
            $geometry = array(
                'type' => 'Point',
                'coordinates' => array(
                    floatval($dataRow[$geo['longitude']]),
                    floatval($dataRow[$geo['latitude']])));
        } elseif (!empty($geo['point'])) {
            array_push($identifiers, $geo['point']);
            $coords = explode(',', $dataRow[$geo['point']]);
            $geometry = array(
                'type' => 'Point',
                'coordinates' => array($coords[0], $coords[1])
            );
        } elseif (!empty($geo['multiline'])) {
            array_push($identifiers, $geo['multiline']);
            $coords = $dataRow[$geo['multiline']];
            $geometry = array(
                'type' => 'LineString',
                'coordinates' => self::convertCoordinateArray($coords)
            );
        } elseif (!empty($geo['polyline'])) {
            array_push($identifiers, $geo['polyline']);
            $geometry = array(
                'type' => 'MultiLineString',
                'coordinates' => self::convertCoordinateMultiArray($dataRow[$geo['polyline']])
            );
        } elseif (!empty($geo['polygon'])) {
            array_push($identifiers, $geo['polygon']);
            $geometry = array(
                'type' => 'Polygon',
                'coordinates' => self::convertCoordinateMultiArray($dataRow[$geo['polygon']])
            );
        }
        return array($identifiers, $geometry);
    }

    /**
     * @param $str string eg: "1.1,2.2 3.3,4.4"
     * @return array eg: ((1.1, 2.2), (3.3, 4.4))
     */
    public static function convertCoordinateArray($str)
    {
        $result = array();
        foreach (explode(' ', $str) as $coordinateStr) {
            $coordinateStrArray = explode(',', $coordinateStr);
            array_push($result, array(floatval($coordinateStrArray[0]), floatval($coordinateStrArray[1])));
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
        foreach (explode(';', $str) as $coordinateArrayStr) {
            array_push($result, self::convertCoordinateArray($coordinateArrayStr));
        }
        return $result;
    }

    public static function getDocumentation()
    {
        return "Returns a GeoJSON document.";
    }
}
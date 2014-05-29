<?php

namespace Tdt\Core\Formatters;

/**
 * KML Formatter
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 */
class KMLFormatter implements IFormatter
{

    private static $LONGITUDE_PREFIXES = array('long', 'lon', 'longitude', 'lng');
    private static $LATITUDE_PREFIXES = array('lat', 'latitude');

    private static $definition;

    public static function createResponse($dataObj)
    {
        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'application/vnd.google-earth.kml+xml;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj)
    {
        self::$definition = $dataObj->definition;

        // Build the body
        // KML header
        $body = '<?xml version="1.0" encoding="UTF-8" ?>';
        $body .= '<kml xmlns="http://www.opengis.net/kml/2.2">';

        // Add the document
        $body .= "<Document>";

        $body .= self::getPlacemarks($dataObj);

        // Close tags
        $body .= "</Document>";
        $body .= "</kml>";

        return $body;
    }

    private static function getPlacemarks($dataObj)
    {

        $data = $dataObj->data;
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        // If no geo property is given, don't bother creating a KML
        if (empty($dataObj->geo)) {

            $placemarks = "";
            ob_start();
            self::printArray($dataObj->data, $placemarks);
            $placemarks = ob_get_contents();
            ob_end_clean();

            return $placemarks;
        }

        return self::getArray($dataObj, $dataObj->geo);
    }

    private static function xmlgetelement($value)
    {
        // TODO decide what to do with the CDATA element
        $result = "<![CDATA[";
        $result .= "]]>";
        return $result;
    }

    private static function getExtendedDataElement($value)
    {
        // TODO decide what to do with extended data element
        $result = "<ExtendedData>";
        $result .= "</ExtendedData>";
        return $result;
    }

    private static function printArray($val, $placemarks)
    {

        foreach ($val as $key => $value) {

            $long = "";
            $lat = "";
            $coords = array();

            if (is_array($value)) {
                $array = $value;
            }
            if (is_object($value)) {
                $array = get_object_vars($value);
            }

            if (!empty($array)) {

                $longkey = false;
                $latkey = false;

                foreach (self::$LONGITUDE_PREFIXES as $prefix) {

                    $longkey = self::keyExists($prefix, $array);

                    if ($longkey) {
                        break;
                    }
                }

                foreach (self::$LATITUDE_PREFIXES as $prefix) {

                    $latkey = self::keyExists($prefix, $array);

                    if ($latkey) {
                        break;
                    }
                }

                $coordskey = self::keyExists("coords", $array);

                if (!$coordskey) {
                    $coordskey = self::keyExists("coordinates", $array);
                }

                if ($longkey && $latkey) {

                    $long = $array[$longkey];
                    $lat = $array[$latkey];

                    unset($array[$longkey]);
                    unset($array[$latkey]);

                    $name = self::xmlgetelement($array);
                    $extendeddata = self::getExtendedDataElement($array);
                } elseif ($coordskey) {

                    $coords = explode(";", $array[$coordskey]);
                    unset($array[$coordskey]);
                    $name = self::xmlgetelement($array);
                    $extendeddata = self::getExtendedDataElement($array);
                } else {
                    self::printArray($array, $placemarks);
                }

                if (($lat != "" && $long != "") || count($coords) != 0) {

                    $name = htmlspecialchars($key);

                    if (!empty(self::$definition['map_property']) && !empty($array[self::$definition['map_property']])) {
                        $name = $array[self::$definition['map_property']];
                    }

                    $description = '';

                    if (!empty($key) && is_numeric($key)) {
                        $description = "<![CDATA[<a href='" . \URL::to(self::$definition['collection_uri'] . '/' . self::$definition['resource_name']) . '/' .  htmlspecialchars($key)  . ".map'>". \URL::to(self::$definition['collection_uri'] . '/' . self::$definition['resource_name']) . '/' .  htmlspecialchars($key) ."</a>]]>";
                    }

                    echo "<Placemark><name>" . $name . "</name><Description>" . $description . "</Description>";

                    echo $extendeddata;

                    if ($lat != "" && $long != "") {

                        // For data read from XML latitude and longitude will be an array of @value = 3.342...

                        if (is_array($lat)) {
                            $lat_val = reset($lat);
                        } else {
                            $lat_val = $lat;
                        }

                        if (is_array($long)) {
                            $lon_val = reset($long);
                        } else {
                            $lon_val = $long;
                        }

                        if ($lat_val != 0 || $lon_val != 0) {
                            echo "<Point><coordinates>" . $lon_val . "," . $lat_val . "</coordinates></Point>";
                        }
                    }

                    if (count($coords)  > 0) {
                        if (count($coords)  == 1) {
                            echo "<Polygon><outerBoundaryIs><LinearRing><coordinates>" . $coords[0] . "</coordinates></LinearRing></outerBoundaryIs></Polygon>";
                        } else {
                            echo "<MultiGeometry>";
                            foreach ($coords as $coord) {
                                echo "<LineString><coordinates>" . $coord . "</coordinates></LineString>";
                            }
                            echo "</MultiGeometry>";
                        }
                    }
                    echo "</Placemark>";
                }
            }
        }
    }

    /**
     * Create the geo graphical placemarks in kml
     * Currently only properties that are not nested are picked up.
     */
    private static function getArray($dataObj, $geo)
    {
        $body = "";

        $data = $dataObj->data;

        if (is_object($data)) {
            $data = (array) $data;
        }

        foreach ($data as $key => $value) {

            if (is_array($value)) {
                $entry = $value;
            } elseif (is_object($value)) {
                $entry = get_object_vars($value);
            }

            // We assume that if longitude exists, latitude does as well if the geometry is a single point
            // A point can either be a single column value, or split up in a latitude and longitude
            $geo_type = 'point';
            $is_point = (count($geo) > 1) || !empty($geo['point']);

            if (!$is_point) {
                $geo_type = key($geo);
                $column_name = $geo[$geo_type];
            }

            if (!empty($entry)) {

                $name = htmlspecialchars($key);

                if (!empty($entry['name'])) {
                    $name = $entry['name'];
                }

                $body .= implode($entry);


                if (!empty($dataObj->definition['map_property']) && !empty($entry[$dataObj->definition['map_property']])) {
                    $name = $entry[$dataObj->definition['map_property']];
                }

                $extendeddata = self::getExtendedDataElement($entry);

                $description = "";

                if (!empty($key) && is_numeric($key)) {
                    $description = "<![CDATA[<a href='" . \URL::to($dataObj->definition['collection_uri'] . '/' . $dataObj->definition['resource_name']) . '/' .  htmlspecialchars($key)  . ".map'>". \URL::to($dataObj->definition['collection_uri'] . '/' . $dataObj->definition['resource_name']) . '/' .  htmlspecialchars($key) ."</a>]]>";
                }

                $body .= "<Placemark><name>" . $name . "</name><description>" . $description . "</description>";
                $body .= $extendeddata;

                if ($is_point) {

                    if (count($geo) > 1) {
                        $point = $entry[$geo['longitude']] . ',' . $entry[$geo['latitude']];
                    } else {
                        $point = $entry[$geo['point']];
                    }

                    $body .= "<Point><coordinates>" . $point . "</coordinates></Point>";
                } else {
                    if ($geo_type == 'polyline') {

                        $body .= "<MultiGeometry>";
                        foreach (explode(';', $entry[$geo['polyline']]) as $coord) {
                            $body .= "<LineString><coordinates>".$coord."</coordinates></LineString>";
                        }
                        $body .= "</MultiGeometry>";

                    } elseif ($geo_type == 'polygon') {
                        $body .= "<Polygon><outerBoundaryIs><LinearRing><coordinates>". $entry[$geo['polygon']] ."</coordinates></LinearRing></outerBoundaryIs></Polygon>";
                    } else {
                        \App::abort(500, "The geo type, $geo_type, is not supported. Make sure the (combined) geo type is correct. (e.g. latitude and longitude are given).");
                    }
                }
                $body .= "</Placemark>";
            }
        }

        return $body;
    }

    /**
     * Case insensitive version of array_key_exists.
     * Returns the matching key on success, else false.
     *
     * @param string $key
     * @param array $search
     * @return string|false
     */
    private static function keyExists($key, $search)
    {

        if (array_key_exists($key, $search)) {
            return $key;
        }

        if (!(is_string($key) && is_array($search) && count($search))) {
            return false;
        }

        $key = strtolower($key);

        foreach ($search as $k => $v) {
            if (strtolower($k) == $key) {
                return $k;
            }
        }
        return false;
    }


    public static function getDocumentation()
    {
        return "Returns a KML file with geo properties of the data.";
    }
}

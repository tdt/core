<?php

namespace Tdt\Core\Formatters;

use Tdt\Core\Formatters\XMLFormatter;

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
    private static $definition;
    private static $map_property;

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
         // Check if the original data is not GeoJSON
        if ($dataObj->source_definition['type'] == 'XML' && !empty($dataObj->geo_formatted) && $dataObj->geo_formatted) {
            return $dataObj->data;
        }

        self::$definition = $dataObj->definition;
        self::$map_property = $dataObj->source_definition['map_property'];

        // Build the body
        // KML header
        $body = '<?xml version="1.0" encoding="UTF-8" ?>';
        $body .= '<kml xmlns="http://www.opengis.net/kml/2.2">';

        // Add the document
        $body .= self::getPlacemarks($dataObj);

        // Close tags
        $body .= "</kml>";

        return $body;
    }

    private static function getPlacemarks($dataObj)
    {
        // If no geo property is given, don't bother creating a KML
        if (empty($dataObj->geo)) {
            ob_start();
            self::printArray($dataObj->data);
            $placemarks = ob_get_contents();
            ob_end_clean();
            return $placemarks;
        }

        return self::getArray($dataObj, $dataObj->geo);
    }

    private static function xmlgetelement($value)
    {
        $result = "<![CDATA[";
        $result .= "]]>";
        return $result;
    }

    private static function getExtendedDataElement($value)
    {
        $result = "<ExtendedData>";
        $result .= "</ExtendedData>";
        return $result;
    }

    private static function printArray($val)
    {
        foreach ($val as $key => $value) {
            $array = $value;
            if (is_object($array)) {
                $array = get_object_vars($value);
            }

            $lat_long = GeoHelper::findLatLong($array);

            $coords = array();

            if (!empty($array)) {
                $coordskey = GeoHelper::keyExists("coords", $array);

                if (!$coordskey) {
                    $coordskey = GeoHelper::keyExists("coordinates", $array);
                }

                if ($lat_long) {
                    unset($array[$lat_long[0]]);
                    unset($array[$lat_long[1]]);

                    $name = self::xmlgetelement($array);
                    $extendeddata = self::getExtendedDataElement($array);
                } elseif ($coordskey) {
                    if (is_array($array[$coordskey])) {
                        if (!empty($array[$coordskey]['@text'])) {
                            $array[$coordskey] = $array[$coordskey]['@text'];
                        }
                    }

                    $coords = explode(";", $array[$coordskey]);
                    unset($array[$coordskey]);
                    $name = self::xmlgetelement($array);
                    $extendeddata = self::getExtendedDataElement($array);
                } else {
                    self::printArray($array);
                }

                if ($lat_long || count($coords) != 0) {
                    $name = htmlspecialchars($key);

                    if (!empty(self::$map_property) && !empty($array[self::$map_property])) {
                        $name = $array[self::$map_property];
                    }

                    $description = '';

                    if (!empty($key) && is_numeric($key)) {
                        $description = "<![CDATA[<a href='" . \URL::to(self::$definition['collection_uri'] . '/' . self::$definition['resource_name']) . '/' .  htmlspecialchars($key)  . ".map'>". \URL::to(self::$definition['collection_uri'] . '/' . self::$definition['resource_name']) . '/' .  htmlspecialchars($key) ."</a>]]>";
                    }

                    echo "<Placemark><name>" . $name . "</name><Description>" . $description . "</Description>";

                    echo $extendeddata;

                    if ($lat_long) {
                        // For data read from XML latitude and longitude will be an array of @value = 3.342...
                        $lat_val = $array[$lat_long[0]];
                        $lon_val = $array[$lat_long[1]];
                        if (is_array($lat_val)) {
                            $lat_val = reset($lat);
                        }
                        if (is_array($lon_val)) {
                            $lon_val = reset($lon_val);
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

                if (!empty($dataObj->map_property) && !empty($entry[$dataObj->map_property])) {
                    $name = $entry[$dataObj->map_property];
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

    public static function getDocumentation()
    {
        return "Returns a KML file with geo properties of the data.";
    }
}

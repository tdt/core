<?php

namespace tdt\core\formatters;

/**
 * KML Formatter
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 */
class KMLFormatter implements IFormatter{

    public static function createResponse($dataObj){

        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Content-Type', 'application/vnd.google-earth.kml+xml;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj){


        // Build the body
        // KML header
        $body = '<?xml version="1.0" encoding="UTF-8" ?>';
        $body .= '<kml xmlns="http://www.opengis.net/kml/2.2">';

        // Add the documen
        $body .= "<Document>";


        $body .= self::getPlacemarks($dataObj->data);

        // Close tags
        $body .= "</Document>";
        $body .= "</kml>";

        return $body;
    }

    private static function getPlacemarks($data){
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        return self::getArray($data);
    }

    private static function xmlgetelement($value){
        $result = "<![CDATA[";
        if(is_object($value)){
            $array = get_object_vars($value);
            foreach($array as $key => $val){
                if(is_numeric($key)){
                    $key = "int_" . $key;
                }
                $result .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }else if(is_array($value)){
            foreach($value as $key => $val){
                if(is_numeric($key)){
                    $key = "int_" . $key;
                }
                $result .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }else{
            $result .= $value;
        }
        $result .= "]]>";
        return $result;
    }

    private static function getExtendedDataElement($value){
        $result = "<ExtendedData>";
        if(is_object($value)){
            $array = get_object_vars($value);
            foreach($array as $key => $val){
                if(is_numeric($key)){
                    $key = "int_" . $key;
                }
                $key = htmlspecialchars(str_replace(" ","",$key));
                $val = htmlspecialchars($val);
                $result .= '<Data name="' . $key . '"><value>' . $val . '</value></Data>';
            }
        }else if(is_array($value)){
            foreach($value as $key => $val){
                if(is_numeric($key)){
                    $key = "int_" . $key;
                }
                $key = htmlspecialchars(str_replace(" ","",$key));
                $val = htmlspecialchars($val);
                $result .= '<Data name="' . $key . '"><value>' . $val . '</value></Data>';
            }
        }else{
            $result .= htmlspecialchars($value);
        }
        $result .= "</ExtendedData>";
        return $result;
    }

    private static function getArray($data){

        $body = "";

        foreach($data as $key => $value) {
            $long = "";
            $lat = "";
            $coords = array();
            if(is_array($value)) {
                $array = $value;
            }
            if (is_object($value)) {
                $array = get_object_vars($value);
            }
            if(isset($array)) {
                $longkey = self::array_key_exists_nc("long",$array);
                if (!$longkey) {
                    $longkey = self::array_key_exists_nc("longitude",$array);
                }
                $latkey = self::array_key_exists_nc("lat",$array);
                if (!$latkey) {
                    $latkey = self::array_key_exists_nc("latitude",$array);
                }
                $coordskey = self::array_key_exists_nc("coords",$array);
                if (!$coordskey) {
                    $coordskey = self::array_key_exists_nc("coordinates",$array);
                }
                if($longkey && $latkey) {
                    $long = $array[$longkey];
                    $lat = $array[$latkey];
                    unset($array[$longkey]);
                    unset($array[$latkey]);
                    $name = self::xmlgetelement($array);
                    $extendeddata = self::getExtendedDataElement($array);
                } else if($coordskey) {
                    $coords = explode(";",$array[$coordskey]);
                    unset($array[$coordskey]);
                    $name = self::xmlgetelement($array);
                    $extendeddata = self::getExtendedDataElement($array);
                }
                else {
                    $body .= self::getArray($array);
                }
                if(($lat != "" && $long != "") || count($coords) != 0){
                    $body .= "<Placemark><name>". htmlspecialchars($key) ."</name><Description>".$name."</Description>";
                    $body .= $extendeddata;
                    if($lat != "" && $long != "") {
                        $body .= "<Point><coordinates>".$long.",".$lat."</coordinates></Point>";
                    }
                    if (count($coords)  > 0) {
                        if (count($coords)  == 1) {
                            $body .= "<Polygon><outerBoundaryIs><LinearRing><coordinates>".$coords[0]."</coordinates></LinearRing></outerBoundaryIs></Polygon>";
                        } else {
                            $body .= "<MultiGeometry>";
                            foreach($coords as $coord) {
                                $body .= "<LineString><coordinates>".$coord."</coordinates></LineString>";
                            }
                            $body .= "</MultiGeometry>";
                        }
                    }
                    $body .= "</Placemark>";
                }
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
    private static function array_key_exists_nc($key, $search) {
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


    public static function getDocumentation(){
        return "Formatter will search for locations in the entire object and print them as KML points.";
    }

}

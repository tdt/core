<?php
/**
 * The Kml-formatter is a formatter which will search for location objects throughout the documenttree and return a file with placemarks 
 *
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

/**
 * This class inherits from the abstract Formatter. It will return our resultobject into a kml
 * datastructure.
 */
class KmlFormatter extends AFormatter{

    public function __construct($rootname,$objectToPrint){
        parent::__construct($rootname,$objectToPrint);
    }

    public function printHeader(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/vnd.google-earth.kml+xml; charset=utf-8");
    }

    public function printBody(){
        /*
         * print the KML header first
         */
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
        echo "<kml xmlns=\"http://www.opengis.net/kml/2.2\">";
        /*
         * Second step is to check every locatable object and print it
         */
        echo "<Document>";

        $this->printPlacemarks($this->objectToPrint);
        echo "</Document>";

        echo "</kml>";
    }
     

    /**
     * The first parameter is the name of an object. The second is an !object!
     */
    private function printPlacemarks($val){
        $hash = get_object_vars($val);
        $this->printArray($hash);
    }

    private function xmlgetelement($value){
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

    private function getExtendedDataElement($value){
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

    private function printArray(&$val){
//	var_dump($val);
	foreach($val as $key => &$value) {
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
                $longkey = $this->array_key_exists_nc("long",$array);
                if (!$longkey) {
                    $longkey = $this->array_key_exists_nc("longitude",$array);			   
                }
                $latkey = $this->array_key_exists_nc("lat",$array);
                if (!$latkey) {
                    $latkey = $this->array_key_exists_nc("latitude",$array);			   
                }
                $coordskey = $this->array_key_exists_nc("coords",$array);
                if (!$coordskey) {
                    $coordskey = $this->array_key_exists_nc("coordinates",$array);			   
                }
                if($longkey && $latkey) {
                    $long = $array[$longkey];
                    $lat = $array[$latkey];
                    unset($array[$longkey]);
                    unset($array[$latkey]);
                    $name = $this->xmlgetelement($array);
                    $extendeddata = $this->getExtendedDataElement($array);				   
                } else if($coordskey) {
                    $coords = explode("|",$array[$coordskey]);
                    unset($array[$coordskey]);
                    $name = $this->xmlgetelement($array);
                    $extendeddata = $this->getExtendedDataElement($array);				   
                }
                else {
                    $this->printArray($array);
                }
                if(($lat != "" && $long != "") || count($coords) != 0){
                    echo "<Placemark><name>$key</name><Description>".$name."</Description>";
                    echo $extendeddata;
                    if($lat != "" && $long != "") {
                        echo "<Point><coordinates>".$long.",".$lat."</coordinates></Point>";
                    }
                    if (count($coords)  > 0) {
                        if (count($coords)  == 1) {
                            echo "<Polygon><outerBoundaryIs><LinearRing><coordinates>".$coords[0]."</coordinates></LinearRing></outerBoundaryIs></Polygon>";
                        } else {
                            echo "<MultiGeometry>";
                            foreach($coords as $coord) {
                                echo "<Polygon><outerBoundaryIs><LinearRing><coordinates>".$coord."</coordinates></LinearRing></outerBoundaryIs></Polygon>";					                            
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
     * Case insensitive version of array_key_exists.
     * Returns the matching key on success, else false.
     *
     * @param string $key
     * @param array $search
     * @return string|false
     */
    private function array_key_exists_nc($key, $search) {
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
        return "Will try to find locations in the entire object and print them as KML points";
    }     
};
?>

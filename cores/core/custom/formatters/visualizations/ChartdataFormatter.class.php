<?php

/**
 * The Html formatter formats everything for development purpose
 *
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Lieven Janssen <lieven.janssen@okfn.org>
 */

/**
 * This class inherits from the abstract Formatter. It will generate chart data
 */
class ChartdataFormatter extends AFormatter {

    private $data;
    private $category = "category";
    private $value = array();
	

    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printHeader(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json;charset=UTF-8");	  	  
    }

    public function printBody() {
        $array = get_object_vars($this->objectToPrint);
        if(array_key_exists("category", $_GET)) {
            $this->category = $_GET["category"];
        }
        if(array_key_exists("value", $_GET)) {
            $this->value = explode(",",$_GET["value"]);
        } else {
            $this->getValues($array);
        }
        if(array_key_exists("refresh", $_GET)) {
            $this->refresh = $_GET["refresh"];
        }
        if(array_key_exists("sort", $_GET)) {
            $this->sort = $_GET["sort"];
        }
		

        $this->data = '[';
        $this->getChartData($array);		
        $this->data .= ']';
        echo $this->data;
    }
    
    public static function getDocumentation(){
        return "A formatter which feeds the extjs charts";
    }
	
    private function getValues($array) {
        foreach($array as $key => $val){
            if(is_object($val)){
                $array = get_object_vars($val);
                $this->getValues($array);
                break;
            } else if(is_array($val)) {
                $array = $val;
                $this->getValues($array);
                break;
            } else {
                if($this->startsWith($key, "value")) {
                    array_push($this->value,$key);
                }
            }
        }
    }
	
    private function startsWith($haystack, $needle) {
        return (strpos($haystack, $needle) === 0);
    }

    private function getChartData($array) {
        $data = "";
        $childdata = "";
        $firstrow = true;
        foreach($array as $key => $val){
            if(is_object($val)){
                $array = get_object_vars($val);
                $childdata = $this->getChartData($array);
                if ($childdata != "") {
                    if (!$firstrow) {
                        $this->data .= ",";
                    }
                    $this->data .= '{' . $childdata . '}';
                    $firstrow = false;
                }
            } else if(is_array($val)) {
                $array = $val;
                $childdata = $this->getChartData($array);
                if ($childdata != "") {
                    if (!$firstrow) {
                        $this->data .= ",";
                    }
                    $this->data .= '{' . $childdata . '}';
                    $firstrow = false;
                }
            } else {
                if($key == $this->category || in_array($key, $this->value)) {
                    if (!$firstrow) {
                        $data .= ",";
                    }
                    $data .= "\"" . $key . "\":\"" . $val . "\"";
                    $firstrow = false;
                }
            }
        }
        return $data;
    }	
}
?>

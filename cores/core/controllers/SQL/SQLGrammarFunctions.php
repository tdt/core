<?php
/**
 * This file is used by the grammar to create the tree
 *
 * @package The-Datatank/controllers/SQL
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

include_once("universalfilter/UniversalFilters.php");

/**
 * This function appends a filter to the list of filters
 * (But only if the filter to append is not null)
 */
function putFilterAfterIfExists($filter, $filterToPutAfter){
    if($filterToPutAfter!=null){
        if($filterToPutAfter->getSource()==null){
            $filterToPutAfter->setSource($filter);
        }else{
            putFilterAfterIfExists($filter, $filterToPutAfter->getSource());
        }
        return $filterToPutAfter;
    }else{
        return $filter;
    }
}

/**
 * Converts the regex from the normal format to the format used in Universal
 */
function convertRegexFromSQLToUniversal($SQLRegex){
    $phpregex = preg_quote($SQLRegex, "/");
    $phpregex = str_replace("%", ".*", $phpregex);
    $phpregex = str_replace("?", ".", $phpregex);
    $phpregex = "/".$phpregex."/";
    return $phpregex;
}

/**
 * Gets the  filter for a nulary SQLFunction
 */
function getNularyFilterForSQLFunction($SQLname){
    $SQLname=strtoupper($SQLname);
    
    if(
            $SQLname=="NOW" || 
            $SQLname=="CURRENT_TIMESTAMP" || 
            $SQLname=="LOCALTIME" || 
            $SQLname=="LOCALTIMESTAMP") {
        return CombinedFilterGenerators::makeDateTimeNow();
    }else if(
            $SQLname=="CURDATE" ||
            $SQLname=="CUR_DATE" ||
            $SQLname=="CURRENT_DATE") {
        return CombinedFilterGenerators::makeDateTimeCurrentDate();
    }else if(
            $SQLname=="CURTIME" ||
            $SQLname=="CUR_TIME" ||
            $SQLname=="CURRENT_TIME") {
        return CombinedFilterGenerators::makeDateTimeCurrentTime();
    }else{
        throw new Exception("That nulary function does not exist... (".$SQLname.")");
    }
}

/**
 * Gets the universal name (and filter) for a unary SQLFunction
 */
function getUnaryFilterForSQLFunction($SQLname, $arg1){
    $SQLname=strtoupper($SQLname);
    
    $unarymap = array(
        "UCASE" => UnaryFunction::$FUNCTION_UNARY_UPPERCASE,
        "UPPER" => UnaryFunction::$FUNCTION_UNARY_UPPERCASE,
        "LCASE" => UnaryFunction::$FUNCTION_UNARY_LOWERCASE,
        "LOWER" => UnaryFunction::$FUNCTION_UNARY_LOWERCASE,
        "LEN" => UnaryFunction::$FUNCTION_UNARY_STRINGLENGTH,
        "ROUND" => UnaryFunction::$FUNCTION_UNARY_ROUND,
        "ISNULL" => UnaryFunction::$FUNCTION_UNARY_ISNULL,
        "NOT" => UnaryFunction::$FUNCTION_UNARY_NOT,
        "SIN" => UnaryFunction::$FUNCTION_UNARY_SIN,
        "COS" => UnaryFunction::$FUNCTION_UNARY_COS,
        "TAN" => UnaryFunction::$FUNCTION_UNARY_TAN,
        "ASIN" => UnaryFunction::$FUNCTION_UNARY_ASIN,
        "ACOS" => UnaryFunction::$FUNCTION_UNARY_ACOS,
        "ATAN" => UnaryFunction::$FUNCTION_UNARY_ATAN,
        "SQRT" => UnaryFunction::$FUNCTION_UNARY_SQRT,
        "ABS" => UnaryFunction::$FUNCTION_UNARY_ABS,
        "FLOOR" => UnaryFunction::$FUNCTION_UNARY_FLOOR,
        "CEIL" => UnaryFunction::$FUNCTION_UNARY_CEIL,
        "EXP" => UnaryFunction::$FUNCTION_UNARY_EXP,
        "LOG" => UnaryFunction::$FUNCTION_UNARY_LOG,
        "PARSE_DATETIME" => UnaryFunction::$FUNCTION_UNARY_DATETIME_PARSE,
        "STR_TO_DATE" => UnaryFunction::$FUNCTION_UNARY_DATETIME_PARSE,
        "DATEPART" => UnaryFunction::$FUNCTION_UNARY_DATETIME_DATEPART,
        "DATE" => UnaryFunction::$FUNCTION_UNARY_DATETIME_DATEPART
    );
    $unaryaggregatormap = array(
        "AVG" => AggregatorFunction::$AGGREGATOR_AVG,
        "COUNT" => AggregatorFunction::$AGGREGATOR_COUNT,
        "FIRST" => AggregatorFunction::$AGGREGATOR_FIRST,
        "LAST" => AggregatorFunction::$AGGREGATOR_LAST,
        "MAX" => AggregatorFunction::$AGGREGATOR_MAX,
        "MIN" => AggregatorFunction::$AGGREGATOR_MIN,
        "SUM" => AggregatorFunction::$AGGREGATOR_SUM
    );
    $formatshortcuts = array(
        "DAY" => array("j"),
        "DAYOFMONTH" => array("j"),
        "DAYOFWEEK" => array("N", array("add" => 1,"max" => 7, "min" => 1)),// (php: 1=Monday ipv mySQL: 1=Sunday)
        "DAYOFYEAR" => array("z", array("add" => 1)),// php: starts from 1, mySQL: starts from 0
        "HOUR" => array("G"),
        "MINUTE" => array("i"),
        "MONTH" => array("n"),//php: starts from 1, mySQL: starts from 1
        "MONTHNAME" => array("F"),
        "SECOND" => array("s"),
        "WEEK" => array("W"),
        "WEEKOFYEAR" => array("W"),
        "WEEKDAY" => array("N", array("add" => 1,"max" => 7, "min" => 1)),//see DAYOFWEEK
        "YEAR" => array("Y"),
        "YEARWEEK" => array("oW")
    );
    
    
    if(isset($formatshortcuts[$SQLname])){
        $functioninfo = $formatshortcuts[$SQLname];
        $funct = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_DATETIME_FORMAT, $arg1, new Constant($functioninfo[0]));
        if(count($functioninfo)>1){
            $extraoperations = $functioninfo[1];
            $addcount = $extraoperations["add"];
            $funct = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_PLUS, $funct, new Constant($addcount));
            if(isset($extraoperations["max"]) && isset($extraoperations["min"])) {
                $max = new Constant($extraoperations["max"]);
                $min = new Constant($extraoperations["min"]);
                $funct=CombinedFilterGenerators::makeWrapInRangeFilter($funct, $min, $max);
            }
        }
        return $funct;
    }else if(isset($unarymap[$SQLname])){
        return new UnaryFunction($unarymap[$SQLname], $arg1);
    }else if(isset($unaryaggregatormap[$SQLname])){
        return new AggregatorFunction($unaryaggregatormap[$SQLname], $arg1);
    }else{
        throw new Exception("That unary function does not exist... (".$SQLname.")");
    }
    
}

/**
 * Gets the universal name (and filter) for a binary SQLFunction
 */
function getBinaryFunctionForSQLFunction($SQLname, $arg1, $arg2){
    //all binary functions like "+", "*", ... are defined in the grammar
    $SQLname=strtoupper($SQLname);
    
    $binarymap = array(
        "REGEX_MATCH" => BinaryFunction::$FUNCTION_BINARY_MATCH_REGEX,
        "ATAN2" => BinaryFunction::$FUNCTION_BINARY_ATAN2,
        "LOG" => BinaryFunction::$FUNCTION_BINARY_LOG,
        "POW" => BinaryFunction::$FUNCTION_BINARY_POW,
        "PARSE_DATETIME" => BinaryFunction::$FUNCTION_BINARY_DATETIME_PARSE,
        "STR_TO_DATE" => BinaryFunction::$FUNCTION_BINARY_DATETIME_PARSE,
        "DATE_FORMAT" => BinaryFunction::$FUNCTION_BINARY_DATETIME_FORMAT,
        "DATEDIFF" => BinaryFunction::$FUNCTION_BINARY_DATETIME_DATEDIFF
    );
    
    if(isset($binarymap[$SQLname])){
        return new BinaryFunction($binarymap[$SQLname], $arg1, $arg2);
    }else{
        throw new Exception("That binary function does not exist... (".$SQLname.")");
    }
}

/**
 * Gets the universal name (and filter) for a tertary SQLFunction
 */
function getTernaryFunctionForSQLFunction($SQLname, $arg1, $arg2, $arg3){
    $SQLname=strtoupper($SQLname);
    
    $tertarymap = array(

        "MID" => TertairyFunction::$FUNCTION_TERTIARY_SUBSTRING,
		"SUBSTRING" => TertairyFunction::$FUNCTION_TERTIARY_SUBSTRING, // TODO: remove this comment: Jeroen, I've also added SUBSTRING to this bunch of ternary functions!
        "REGEX_REPLACE" => TertairyFunction::$FUNCTION_TERTIARY_REGEX_REPLACE
    );
    
    if(isset($tertarymap[$SQLname])){
        return new TernaryFunction($tertarymap[$SQLname], $arg1,$arg2,$arg3);
    }else{
        throw new Exception("That ternary function does not exist... (".$SQLname.")");
    }
}

function getQuadernaryFunctionForSQLFunction($SQLname, $arg1, $arg2, $arg3, $arg4){
    $SQLname=strtoupper($SQLname);
    
    if($SQLname=="GEODISTANCE"){
        return CombinedFilterGenerators::makeGeoDistanceFilter($arg1, $arg2, $arg3, $arg4);
    }else{
        throw new Exception("That tertary function does not exist... (".$SQLname.")");
    }
}

function getExtractConstant($string){
    return new Constant($string);
}

function getExtractFunction($string, $constant) {
    return new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_DATETIME_EXTRACT, $string, $constant);
}

function getDateAddFunction($isadd, $date, $interval, $intervaltype) {
    $type = TernaryFunction::$FUNCTION_TERNARY_DATETIME_DATEADD;
    if(!$isadd) {
        $type = TernaryFunction::$FUNCTION_TERNARY_DATETIME_DATESUB;
    }
    return new TernaryFunction($type, $date, $interval, $intervaltype);
}
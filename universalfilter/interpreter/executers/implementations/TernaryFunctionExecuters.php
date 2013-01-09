<?php

/**
 * This file contains all evaluators for ternary functions
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\interpreter\executers\implementations;

/* substring / MID */
class TernaryFunctionSubstringExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\TernaryFunctionExecuter {
    
    public function getName($nameA, $nameB, $nameC){
        return "substring_".$nameA."_".$nameB."_".$nameC;
    }
    
    public function doTernaryFunction($valueA, $valueB, $valueC){
        if($valueA===null || $valueB===null || $valueC===null) return null;
        return substr($valueA, $valueB, $valueC);
    }
}

/* regex replace */
class TernaryFunctionRegexReplacementExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\TernaryFunctionExecuter {
    
    public function getName($nameA, $nameB, $nameC){
        return $nameA."_replaced_".$nameB."_with_".$nameC;
    }
    
    public function doTernaryFunction($valueA, $valueB, $valueC){
        if($valueA===null || $valueB===null || $valueC===null) return null;
        return preg_replace($valueA, $valueB, $valueC);
    }
}

/* date add */
class TernaryFunctionDateTimeDateAddExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\TernaryFunctionExecuter {
    
    public function getName($nameA, $nameB, $nameC){
        return "_date_add_".$nameA."_interval_".$nameB."_".$nameC;
    }
    
    public function doTernaryFunction($valueA, $valueB, $valueC){
        if($valueA===null || $valueB===null || $valueC===null) return null;
        $dateTime = tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools::getDateTime($valueA, "date_add");
        $interval = tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools::toInterval($valueB, $valueC);
        return $dateTime->add($interval)->format(tdt\core\universalfilter\interpreter\UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}

/* date sub */
class TernaryFunctionDateTimeDateSubExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\TernaryFunctionExecuter {
    
    public function getName($nameA, $nameB, $nameC){
        return "_date_sub_".$nameA."_interval_".$nameB."_".$nameC;
    }
    
    public function doTernaryFunction($valueA, $valueB, $valueC){
        if($valueA===null || $valueB===null || $valueC===null) return null;
        $dateTime = tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools::getDateTime($valueA, "date_sub");
        $interval = tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools::toInterval($valueB, $valueC);
        return $dateTime->sub($interval)->format(tdt\core\universalfilter\interpreter\UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}

?>

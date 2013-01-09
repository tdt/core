<?php

/**
 * This file contains all evaluators for unary functions
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\interpreter\executers\implementations;

/* upercase */
class UnaryFunctionUppercaseExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "uppercase_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return strtoupper($value);
    }
}

/* lowercase */
class UnaryFunctionLowercaseExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "lowercase_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return strtolower($value);
    }
}

/* stringlength */
class UnaryFunctionStringLengthExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "length_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return strlen($value);
    }
}

/* round */
class UnaryFunctionRoundExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "round_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return round($value);
    }
}

/* isnull */
class UnaryFunctionIsNullExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "isnull_".$name;
    }
    
    public function doUnaryFunction($value){
        return (is_null($value)?"true":"false");
    }
}

/* not */
class UnaryFunctionNotExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "not_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return ($value=="true" || $value==1?"false":"true");
    }
}

/* sin */
class UnaryFunctionSinExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "sin_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".sin($value);
    }
}

/* cos */
class UnaryFunctionCosExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "cos_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".cos($value);
    }
}

/* tan */
class UnaryFunctionTanExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "tan_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".tan($value);
    }
}

/* asin */
class UnaryFunctionAsinExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "asin_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".asin($value);
    }
}

/* acos */
class UnaryFunctionAcosExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "acos_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".acos($value);
    }
}

/* atan */
class UnaryFunctionAtanExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "atan_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".atan($value);
    }
}

/* sqrt */
class UnaryFunctionSqrtExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "sqrt_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".sqrt($value);
    }
}

/* abs */
class UnaryFunctionAbsExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "abs_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".abs($value);
    }
}

/* floor */
class UnaryFunctionFloorExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "floor_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".floor($value);
    }
}

/* ceil */
class UnaryFunctionCeilExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "ceil_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".ceil($value);
    }
}

/* exp */
class UnaryFunctionExpExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "exp_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".exp($value);
    }
}

/* log */
class UnaryFunctionLogExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "log_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        return "".log($value);
    }
}

/*
 * DateTimeFunctions
 */

/* datepart */
class UnaryFunctionDatePartExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "datepart_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        $dateTime = tdt\core\universalfilter\interpreter\tools\ExecuterDateTimeTools::getDateTime($value, "datepart");
        $dateOnlyDateTime = new DateTime($dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT_ONLYDATE));
        return $dateOnlyDateTime->format(tdt\core\universalfilter\interpreter\UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}

/* parse_datetime */
class UnaryFunctionParseDateTimeExecuter extends tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter {
    
    public function getName($name){
        return "parse_datetime_".$name;
    }
    
    public function doUnaryFunction($value){
        if($value===null) return null;
        $dateTime = new DateTime($value);
        return $dateTime->format(tdt\core\universalfilter\interpreter\UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}
?>
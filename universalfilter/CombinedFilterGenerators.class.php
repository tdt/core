<?php

/**
 * This file contains some methods to create some useful combinations of the nodes in the UniversalFilterTree
 *
 * @package The-Datatank/universalfilter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\tablemanager;

class CombinedFilterGenerators {
    
    /**
     * This function sets the source of a combined filer
     * @param NormalFilterNode $for
     * @param UniversalFilterNode $sourceToSet 
     */
    public static function setCombinedFilterSource(tdt\core\universalfilter\NormalFilterNode $for, tdt\core\universalfilter\UniversalFilterNode $sourceToSet){
        if($for->getSource()===null){
            $for->setSource($filter);
        }else{
            CombinedFilterGenerators::setCombinedFilterSource($for->getSource(), $sourceToSet);
        }
    }
    
    /**
     * Creates a BETWEEN-filter  (inclusive!)
     * 
     * @param UniversalFilterNode $a what to filter
     * @param UniversalFilterNode $b left bound
     * @param UniversalFilterNode $c right bound
     * @return NormalFilterNode the filter
     */
    public static function makeBetweenFilter(tdt\core\universalfilter\UniversalFilterNode $a, tdt\core\universalfilter\UniversalFilterNode $b, tdt\core\universalfilter\UniversalFilterNode $c){
        return new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_AND, 
            new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN, $b, $a), 
            new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN, $a, $c));
    }
    
    /**
     * Creates a smaller (or equal) than ALL/ANY  filter
     * 
     * @param UniversalFilterNode $a the left side
     * @param UniversalFilterNode $b the right side
     * @param boolean $strictSmaller <= or <
     * @param boolean $isAllFilter ALL or ANY ?
     * @return NormalFilterNode the filter
     */
    public static function makeSmallerThanAllOrAnyFilter(tdt\core\universalfilter\UniversalFilterNode $a, tdt\core\universalfilter\UniversalFilterNode $b, $strictSmaller=true,  $isAllFilter=true){
        $aggr = ($isAllFilter?tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_MIN:tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_MAX);
        $function = ($strictSmaller?tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_THAN:tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN);
        return new tdt\core\universalfilter\BinaryFunction($function, $a, new tdt\core\universalfilter\AggregatorFunction($aggr, $b));
    }
    
    /**
     * Creates a larger (or equal) than ALL/ANY  filter
     * 
     * @param UniversalFilterNode $a the left side
     * @param UniversalFilterNode $b the right side
     * @param boolean $strictLarger >= or >
     * @param boolean $isAllFilter ALL or ANY ?
     * @return NormalFilterNode the filter
     */
    public static function makeLargerThanAllOrAnyFilter(tdt\core\universalfilter\UniversalFilterNode $a, tdt\core\universalfilter\UniversalFilterNode $b, $strictLarger=true,  $isAllFilter=true){
        $aggr = ($isAllFilter?tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_MAX:tdt\core\universalfilter\tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_MIN);
        $function = ($strictLarger?tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_THAN:tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN);
        return new tdt\core\universalfilter\BinaryFunction($function, $a, new tdt\core\universalfilter\AggregatorFunction($aggr, $b));
    }
    
    /**
     * Creates a degree to radians filter
     * 
     * @param UniversalFilterNode $a the argument
     * @return NormalFilterNode the filter
     */
    public static function makeDegreeToRadiansFilter(tdt\core\universalfilter\UniversalFilterNode $a){
        return new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MULTIPLY, $a, new tdt\core\universalfilter\Constant(pi()/180));
    }
    
    /**
     * Creates a EarthDistance filter
     * 
     * Which calculates the distance between ($longA,$latA) and ($longB,$latB)
     * 
     * @param UniversalFilterNode $longA logitude A (in degrees)
     * @param UniversalFilterNode $latA latitude A (in degrees)
     * @param UniversalFilterNode $longB longitude B (in degrees)
     * @param UniversalFilterNode $latB latitude B (in degrees)
     * @return NormalFilterNode the filter
     */
    public static function makeGeoDistanceFilter(tdt\core\universalfilter\UniversalFilterNode $latA, tdt\core\universalfilter\UniversalFilterNode $longA, tdt\core\universalfilter\UniversalFilterNode $latB, tdt\core\universalfilter\UniversalFilterNode $longB){
        /*
         * Based upon code:
         * 
            $olat = $feature->geometry->coordinates[1];
            $olon = $feature->geometry->coordinates[0];
            $R = 6371; // earth's radius in km
            $dLat = deg2rad($this->lat-$olat);
            $dLon = deg2rad($this->long-$olon);
            $rolat = deg2rad($olat);
            $rlat = deg2rad($this->lat);

            $a = sin($dLat/2) * sin($dLat/2) + sin($dLon/2) * sin($dLon/2) * cos($rolat) * cos($rlat);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            $distance = $R * $c;
         */
        
        $thislat = $latB;
        $thislong = $longB;
        
        $olat = $latA;
        $olon = $longA;
        $R = new tdt\core\universalfilter\Constant(6371); // earth's radius in km
        $dLat = CombinedFilterGenerators::makeDegreeToRadiansFilter(new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MINUS, $thislat, $olat));
        $dLon = CombinedFilterGenerators::makeDegreeToRadiansFilter(new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MINUS, $thislong, $olon));
        $rolat = CombinedFilterGenerators::makeDegreeToRadiansFilter($olat);
        $rlat = CombinedFilterGenerators::makeDegreeToRadiansFilter($thislat);
        
        $a = new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_PLUS, 
                new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MULTIPLY, 
                        new tdt\core\universalfilter\UnaryFunction(tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_SIN, 
                                new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_DIVIDE, $dLat, new tdt\core\universalfilter\Constant(2))
                                ),
                        new tdt\core\universalfilter\UnaryFunction(tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_SIN, 
                                new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_DIVIDE, $dLat, new tdt\core\universalfilter\Constant(2))
                                )
                        ), 
                new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MULTIPLY, 
                        new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MULTIPLY, 
                                new tdt\core\universalfilter\UnaryFunction(tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_SIN, 
                                        new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_DIVIDE, $dLon, new tdt\core\universalfilter\Constant(2))
                                        ),
                                new tdt\core\universalfilter\UnaryFunction(tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_SIN, 
                                        new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_DIVIDE, $dLon, new tdt\core\universalfilter\Constant(2))
                                        )
                                ),
                        new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MULTIPLY, 
                                new tdt\core\universalfilter\UnaryFunction(tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_COS, $rolat),
                                new tdt\core\universalfilter\UnaryFunction(tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_COS, $rlat)
                                )
                        )
                );
        $c = new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MULTIPLY, new tdt\core\universalfilter\Constant(2), 
                    new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_ATAN2, 
                            new tdt\core\universalfilter\UnaryFunction(tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_SQRT, $a), 
                            new tdt\core\universalfilter\UnaryFunction(tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_SQRT, 
                                    new tdt\core\universalfilter\BinaryFunction(tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MINUS, new tdt\core\universalfilter\Constant(1), $a))));
        $distance = new tdt\core\universalfilter\BinaryFunction(
                    tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MULTIPLY, 
                    $R,
                    $c);
        return $distance;
    }
    
    /** makes a constant with the current date and time */
    public static function makeDateTimeNow(){
        $dateTime = new DateTime();
        return new tdt\core\universalfilter\Constant($dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT));
    }
    
    /** makes a constant with the current date */
    public static function makeDateTimeCurrentDate(){
        $dateTime = new DateTime();
        $dateOnlyDateTime = new DateTime($dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT_ONLYDATE));
        return new tdt\core\universalfilter\Constant($dateOnlyDateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT));
    }
    
    /** makes a constant with the current time */
    public static function makeDateTimeCurrentTime(){
        $dateTime = new DateTime();
        $timeOnlyDateTime = new DateTime($dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT_ONLYTIME));
        return new tdt\core\universalfilter\Constant($timeOnlyDateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT));
    }
    
    /**
     * Wraps the values of funct to be between a and b
     * 
     * This can be used as modulo if a=0
     * 
     * (Only tested for positive values...)
     */
    public static function makeWrapInRangeFilter(tdt\core\universalfilter\UniversalFilterNode $funct, tdt\core\universalfilter\UniversalFilterNode $min, tdt\core\universalfilter\UniversalFilterNode $max) {
        // the logic:
        //   a = (a-min)%(max-min+1)+min
        // the logic (without modulo): 
        //   a = ((a-min)-floor((a-min)/(max-min+1))*(max-min+1))+min
        
        $r= 
        new tdt\core\universalfilter\BinaryFunction(
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_PLUS, 
            new tdt\core\universalfilter\BinaryFunction(
                tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MINUS, 
                new tdt\core\universalfilter\BinaryFunction(
                    tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MINUS, 
                    $funct, 
                    $min),
                new tdt\core\universalfilter\BinaryFunction(
                    tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MULTIPLY,
                    new tdt\core\universalfilter\UnaryFunction(
                        tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_FLOOR, 
                        new tdt\core\universalfilter\BinaryFunction(
                            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_DIVIDE, 
                            new tdt\core\universalfilter\BinaryFunction(
                                tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MINUS,
                                $funct,
                                $min), 
                            new tdt\core\universalfilter\BinaryFunction(
                                tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_PLUS,
                                new tdt\core\universalfilter\BinaryFunction(
                                    tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MINUS, 
                                    $max, 
                                    $min),
                                new tdt\core\universalfilter\Constant(1)
                                )
                            )
                        ),
                    new tdt\core\universalfilter\BinaryFunction(
                        tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_PLUS,
                        new tdt\core\universalfilter\BinaryFunction(
                            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MINUS, 
                            $max, 
                            $min),
                        new tdt\core\universalfilter\Constant(1)
                        )
                    )
                ),
            $min
            );
        return $r;
    }
}

?>

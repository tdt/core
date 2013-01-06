<?php

/**
 * This file contains some methods to create some useful combinations of the nodes in the UniversalFilterTree
 *
 * @package The-Datatank/universalfilter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class CombinedFilterGenerators {
    
    /**
     * This function sets the source of a combined filer
     * @param NormalFilterNode $for
     * @param UniversalFilterNode $sourceToSet 
     */
    public static function setCombinedFilterSource(NormalFilterNode $for, UniversalFilterNode $sourceToSet){
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
    public static function makeBetweenFilter(UniversalFilterNode $a, UniversalFilterNode $b, UniversalFilterNode $c){
        return new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_AND, 
            new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN, $b, $a), 
            new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN, $a, $c));
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
    public static function makeSmallerThanAllOrAnyFilter(UniversalFilterNode $a, UniversalFilterNode $b, $strictSmaller=true,  $isAllFilter=true){
        $aggr = ($isAllFilter?AggregatorFunction::$AGGREGATOR_MIN:AggregatorFunction::$AGGREGATOR_MAX);
        $function = ($strictSmaller?BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_THAN:BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN);
        return new BinaryFunction($function, $a, new AggregatorFunction($aggr, $b));
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
    public static function makeLargerThanAllOrAnyFilter(UniversalFilterNode $a, UniversalFilterNode $b, $strictLarger=true,  $isAllFilter=true){
        $aggr = ($isAllFilter?AggregatorFunction::$AGGREGATOR_MAX:AggregatorFunction::$AGGREGATOR_MIN);
        $function = ($strictLarger?BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_THAN:BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN);
        return new BinaryFunction($function, $a, new AggregatorFunction($aggr, $b));
    }
    
    /**
     * Creates a degree to radians filter
     * 
     * @param UniversalFilterNode $a the argument
     * @return NormalFilterNode the filter
     */
    public static function makeDegreeToRadiansFilter(UniversalFilterNode $a){
        return new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MULTIPLY, $a, new Constant(pi()/180));
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
    public static function makeGeoDistanceFilter(UniversalFilterNode $latA, UniversalFilterNode $longA, UniversalFilterNode $latB, UniversalFilterNode $longB){
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
        $R = new Constant(6371); // earth's radius in km
        $dLat = CombinedFilterGenerators::makeDegreeToRadiansFilter(new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MINUS, $thislat, $olat));
        $dLon = CombinedFilterGenerators::makeDegreeToRadiansFilter(new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MINUS, $thislong, $olon));
        $rolat = CombinedFilterGenerators::makeDegreeToRadiansFilter($olat);
        $rlat = CombinedFilterGenerators::makeDegreeToRadiansFilter($thislat);
        
        $a = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_PLUS, 
                new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MULTIPLY, 
                        new UnaryFunction(UnaryFunction::$FUNCTION_UNARY_SIN, 
                                new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_DIVIDE, $dLat, new Constant(2))
                                ),
                        new UnaryFunction(UnaryFunction::$FUNCTION_UNARY_SIN, 
                                new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_DIVIDE, $dLat, new Constant(2))
                                )
                        ), 
                new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MULTIPLY, 
                        new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MULTIPLY, 
                                new UnaryFunction(UnaryFunction::$FUNCTION_UNARY_SIN, 
                                        new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_DIVIDE, $dLon, new Constant(2))
                                        ),
                                new UnaryFunction(UnaryFunction::$FUNCTION_UNARY_SIN, 
                                        new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_DIVIDE, $dLon, new Constant(2))
                                        )
                                ),
                        new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MULTIPLY, 
                                new UnaryFunction(UnaryFunction::$FUNCTION_UNARY_COS, $rolat),
                                new UnaryFunction(UnaryFunction::$FUNCTION_UNARY_COS, $rlat)
                                )
                        )
                );
        $c = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MULTIPLY, new Constant(2), 
                    new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_ATAN2, 
                            new UnaryFunction(UnaryFunction::$FUNCTION_UNARY_SQRT, $a), 
                            new UnaryFunction(UnaryFunction::$FUNCTION_UNARY_SQRT, 
                                    new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MINUS, new Constant(1), $a))));
        $distance = new BinaryFunction(
                    BinaryFunction::$FUNCTION_BINARY_MULTIPLY, 
                    $R,
                    $c);
        return $distance;
    }
    
    /** makes a constant with the current date and time */
    public static function makeDateTimeNow(){
        $dateTime = new DateTime();
        return new Constant($dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT));
    }
    
    /** makes a constant with the current date */
    public static function makeDateTimeCurrentDate(){
        $dateTime = new DateTime();
        $dateOnlyDateTime = new DateTime($dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT_ONLYDATE));
        return new Constant($dateOnlyDateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT));
    }
    
    /** makes a constant with the current time */
    public static function makeDateTimeCurrentTime(){
        $dateTime = new DateTime();
        $timeOnlyDateTime = new DateTime($dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT_ONLYTIME));
        return new Constant($timeOnlyDateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT));
    }
    
    /**
     * Wraps the values of funct to be between a and b
     * 
     * This can be used as modulo if a=0
     * 
     * (Only tested for positive values...)
     */
    public static function makeWrapInRangeFilter(UniversalFilterNode $funct, UniversalFilterNode $min, UniversalFilterNode $max) {
        // the logic:
        //   a = (a-min)%(max-min+1)+min
        // the logic (without modulo): 
        //   a = ((a-min)-floor((a-min)/(max-min+1))*(max-min+1))+min
        
        $r= 
        new BinaryFunction(
            BinaryFunction::$FUNCTION_BINARY_PLUS, 
            new BinaryFunction(
                BinaryFunction::$FUNCTION_BINARY_MINUS, 
                new BinaryFunction(
                    BinaryFunction::$FUNCTION_BINARY_MINUS, 
                    $funct, 
                    $min),
                new BinaryFunction(
                    BinaryFunction::$FUNCTION_BINARY_MULTIPLY,
                    new UnaryFunction(
                        UnaryFunction::$FUNCTION_UNARY_FLOOR, 
                        new BinaryFunction(
                            BinaryFunction::$FUNCTION_BINARY_DIVIDE, 
                            new BinaryFunction(
                                BinaryFunction::$FUNCTION_BINARY_MINUS,
                                $funct,
                                $min), 
                            new BinaryFunction(
                                BinaryFunction::$FUNCTION_BINARY_PLUS,
                                new BinaryFunction(
                                    BinaryFunction::$FUNCTION_BINARY_MINUS, 
                                    $max, 
                                    $min),
                                new Constant(1)
                                )
                            )
                        ),
                    new BinaryFunction(
                        BinaryFunction::$FUNCTION_BINARY_PLUS,
                        new BinaryFunction(
                            BinaryFunction::$FUNCTION_BINARY_MINUS, 
                            $max, 
                            $min),
                        new Constant(1)
                        )
                    )
                ),
            $min
            );
        return $r;
    }
}

?>

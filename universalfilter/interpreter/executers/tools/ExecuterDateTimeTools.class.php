<?php
/**
 * Some methods for datetime functions
 *
 * @package The-Datatank/universalfilter/executers/tools
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class ExecuterDateTimeTools {
    
    /**
     * Reads the internal format.
     * 
     * @param string $value
     * @param string $what
     * @return DateTime the datetime in the value
     */
    static function getDateTime($value, $what="this filter"){
        $dateTime = DateTime::createFromFormat(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT, $value);
        if($dateTime===FALSE){
            throw new Exception("Can only use $what on dates. Please convert to date first.");
        }
        return $dateTime;
    }
    
    /**
     * Calculates the days that passed since the start of the year...
     *
     * @param DateTime $datetime 
     * @return int 
     */
    static function daysSinceStartYear(DateTime $datetime){
        $datediff = $datetime->diff(new DateTime($datetime->format("Y")."0101"));
        return $datediff->days;
    }
    
    /**
     * Extracts some part out of the date.
     * 
     * @param DateTime $datetime
     * @param string $constant (see DateTimeExtractConstants)
     * @return string the result
     */
    static function extract($datetime, $constant){
        $formatmap = array(
            DateTimeExtractConstants::$EXTRACT_SECOND => "s",
            DateTimeExtractConstants::$EXTRACT_MINUTE => "i",
            DateTimeExtractConstants::$EXTRACT_HOUR => "G",
            DateTimeExtractConstants::$EXTRACT_DAY => "d",
            DateTimeExtractConstants::$EXTRACT_WEEK => "W",
            DateTimeExtractConstants::$EXTRACT_MONTH => "m",
            DateTimeExtractConstants::$EXTRACT_YEAR => "Y",
            DateTimeExtractConstants::$EXTRACT_MINUTE_SECOND => "i:s",//MINUTES:SECONDS
            DateTimeExtractConstants::$EXTRACT_HOUR_SECOND => "G:i:s",//HOURS:MINUTES:SECONDS
            DateTimeExtractConstants::$EXTRACT_HOUR_MINUTE => "G:i",//HOURS:MINUTES
            DateTimeExtractConstants::$EXTRACT_DAY_SECOND => " G:i:s",//DAYS HOURS:MINUTES:SECONDS
            DateTimeExtractConstants::$EXTRACT_DAY_MINUTE => " G:i",//DAYS HOURS:MINUTES
            DateTimeExtractConstants::$EXTRACT_DAY_HOUR => " G",//DAYS HOURS
            DateTimeExtractConstants::$EXTRACT_YEAR_MONTH => "Y-m"//YEARS-MONTHS
        );
        
        $format = $formatmap[$constant];
        $result = $datetime->format($format);
        if(substr($format, 0, 1)==" "){
            $result = ExecuterDateTimeTools::daysSinceStartYear($datetime).$result;
        }
        
        return $result;
    }
    
    /**
     * Convert a string to an DateInterval-object
     * 
     * @param string $diffstring
     * @param string $constant
     * @return DateInterval 
     */
    static function toInterval($diffstring, $constant) {
        $replacementmap = array(
            DateTimeExtractConstants::$EXTRACT_SECOND => 
                    array("([0-9]+)","$1 seconds"),
            DateTimeExtractConstants::$EXTRACT_MINUTE => 
                    array("([0-9]+)","$1 minutes"),
            DateTimeExtractConstants::$EXTRACT_HOUR => 
                    array("([0-9]+)","$1 hours"),
            DateTimeExtractConstants::$EXTRACT_DAY => 
                    array("([0-9]+)","$1 days"),
            DateTimeExtractConstants::$EXTRACT_WEEK => 
                    array("([0-9]+)","$1 weeks"),
            DateTimeExtractConstants::$EXTRACT_MONTH => 
                    array("([0-9]+)","$1 months"),
            DateTimeExtractConstants::$EXTRACT_YEAR => 
                    array("([0-9]+)","$1 years"),
            DateTimeExtractConstants::$EXTRACT_MINUTE_SECOND => 
                    array("([0-9]+):([0-9]+)","$1 minutes $2 seconds"),//MINUTES:SECONDS
            DateTimeExtractConstants::$EXTRACT_HOUR_SECOND => 
                    array("([0-9]+):([0-9]+):([0-9]+)","$1 hours $2 minutes $3 seconds"),//HOURS:MINUTES:SECONDS
            DateTimeExtractConstants::$EXTRACT_HOUR_MINUTE => 
                    array("([0-9]+):([0-9]+)","$1 hours $2 minutes"),//HOURS:MINUTES
            DateTimeExtractConstants::$EXTRACT_DAY_SECOND => 
                    array("([0-9]+) ([0-9]+):([0-9]+):([0-9]+)","$1 days $2 hours $3 minutes $4 seconds"),//DAYS HOURS:MINUTES:SECONDS
            DateTimeExtractConstants::$EXTRACT_DAY_MINUTE => 
                    array("([0-9]+) ([0-9]+):([0-9]+)","$1 days $2 hours $3 minutes"),//DAYS HOURS:MINUTES
            DateTimeExtractConstants::$EXTRACT_DAY_HOUR => 
                    array("([0-9]+) ([0-9]+)","$1 days $2 hours"),//DAYS HOURS
            DateTimeExtractConstants::$EXTRACT_YEAR_MONTH => 
                    array("([0-9]+)-([0-9]+)","$1 years $2 months")//YEARS-MONTHS
        );
        
        $replacementgroup = $replacementmap[$constant];
        
        $pattern = "#^".$replacementgroup[0]."$#";
        $toreplace = $replacementgroup[1];
        
        $diffstring = preg_replace($pattern, $toreplace, $diffstring);
        
        $dateinterval = DateInterval::createFromDateString($diffstring);
        if($dateinterval===FALSE) {
            throw new Exception("Internal error: unable to convert \"".$diffstring."\" as \"".$constant."\" to interval.");
        }
        return $dateinterval;
    }
}

?>

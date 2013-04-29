<?php
namespace tdt\core\universalfilter;
use tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;
/* parse_datetime */
class UnaryFunctionParseDateTimeExecuter extends UnaryFunctionExecuter {
    public function getName($name) {
        return "parse_datetime_" . $name;
    }
    public function doUnaryFunction($value) {
        if ($value === null)
            return null;
        $dateTime = new DateTime($value);
        return $dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}
?>
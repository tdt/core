<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

/*
 * DateTimeFunctions
 */
/* datepart */
class UnaryFunctionDatePartExecuter extends UnaryFunctionExecuter {
    public function getName($name) {
        return "datepart_" . $name;
    }
    public function doUnaryFunction($value) {
        if ($value === null)
            return null;
        $dateTime = ExecuterDateTimeTools::getDateTime($value, "datepart");
        $dateOnlyDateTime = new DateTime($dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT_ONLYDATE));
        return $dateOnlyDateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}

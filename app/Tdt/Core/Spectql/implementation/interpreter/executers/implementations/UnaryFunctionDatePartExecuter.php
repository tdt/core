<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/*
 * DateTimeFunctions
 */
/* datepart */
class UnaryFunctionDatePartExecuter extends UnaryFunctionExecuter
{
    public function getName($name)
    {
        return "datepart_" . $name;
    }
    public function doUnaryFunction($value)
    {
        if ($value === null)
            return null;
        $dateTime = ExecuterDateTimeTools::getDateTime($value, "datepart");
        $dateOnlyDateTime = new DateTime($dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT_ONLYDATE));
        return $dateOnlyDateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}

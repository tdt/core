<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\TernaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* date add */
class TernaryFunctionDateTimeDateAddExecuter extends TernaryFunctionExecuter
{
    public function getName($nameA, $nameB, $nameC)
    {
        return "_date_add_" . $nameA . "_interval_" . $nameB . "_" . $nameC;
    }
    public function doTernaryFunction($valueA, $valueB, $valueC)
    {
        if ($valueA === null || $valueB === null || $valueC === null)
            return null;
        $dateTime = ExecuterDateTimeTools::getDateTime($valueA, "date_add");
        $interval = ExecuterDateTimeTools::toInterval($valueB, $valueC);
        return $dateTime->add($interval)->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}

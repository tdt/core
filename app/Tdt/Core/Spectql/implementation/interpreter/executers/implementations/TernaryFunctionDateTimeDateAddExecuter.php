<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\TernaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

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

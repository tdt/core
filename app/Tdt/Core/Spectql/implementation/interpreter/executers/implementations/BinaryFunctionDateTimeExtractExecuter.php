<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* extract */
class BinaryFunctionDateTimeExtractExecuter extends BinaryFunctionExecuter
{
    public function getName($nameA, $nameB)
    {
        return "_extract_" . $nameB . "_from_" . $nameA;
    }
    public function doBinaryFunction($valueA, $valueB)
    {
        if ($valueA === null || $valueB === null)
            return null;
        $dateTime = ExecuterDateTimeTools::getDateTime($valueA, "extract");
        return ExecuterDateTimeTools::extract($dateTime, $valueB);
    }
}

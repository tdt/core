<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* atan2 */
class BinaryFunctionAtan2Executer extends BinaryFunctionExecuter
{
    public function getName($nameA, $nameB)
    {
        return "_atan2_" . $nameA . "_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB)
    {
        if ($valueA === null || $valueB === null)
            return null;
        return "" . atan2($valueA, $valueB);
    }
}

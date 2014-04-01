<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* pow */
class BinaryFunctionPowExecuter extends BinaryFunctionExecuter
{
    public function getName($nameA, $nameB)
    {
        return "_power_" . $nameA . "_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB)
    {
        if ($valueA === null || $valueB === null)
            return null;
        return "" . pow($valueA, $valueB);
    }
}

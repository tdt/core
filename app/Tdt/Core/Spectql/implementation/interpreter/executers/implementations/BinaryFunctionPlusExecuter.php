<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* plus */
class BinaryFunctionPlusExecuter extends BinaryFunctionExecuter
{
    public function getName($nameA, $nameB)
    {
        return $nameA . "_plus_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB)
    {
        if ($valueA === null || $valueB === null)
            return null;
        return "" . ($valueA + $valueB);
    }
}

<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* < */
class BinaryFunctionSmallerExecuter extends BinaryFunctionExecuter
{
    public function getName($nameA, $nameB)
    {
        return $nameA . "_issmaller_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB)
    {
        if ($valueA === null || $valueB === null || ($valueA == "null" && is_numeric($valueB)))
            return null;
        return ($valueA < $valueB ? "true" : "false");
    }
}

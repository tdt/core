<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* or */
class BinaryFunctionOrExecuter extends BinaryFunctionExecuter
{
    public function getName($nameA, $nameB)
    {
        return $nameA . "_or_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB)
    {
        if ($valueA == "true" || $valueB == "true") {
            return "true";
        } else {
            return (($valueA === null) || ($valueB === null) ? null : "false");
        }
    }
}

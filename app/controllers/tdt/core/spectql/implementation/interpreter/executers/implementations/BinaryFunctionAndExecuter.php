<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* and */
class BinaryFunctionAndExecuter extends BinaryFunctionExecuter
{
    public function getName($nameA, $nameB) {
        return $nameA . "_and_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        $valueA = ($valueA == "true" ? true : ($valueA === null ? null : false));
        $valueB = ($valueB == "true" ? true : ($valueB === null ? null : false));
        if ($valueA === false || $valueB === false) {
            return "false";
        } else {
            return (($valueA === null) || ($valueB === null) ? null : "true");
        }
    }
}

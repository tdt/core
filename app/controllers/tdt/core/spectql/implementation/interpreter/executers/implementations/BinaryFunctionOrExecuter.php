<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* or */
class BinaryFunctionOrExecuter extends BinaryFunctionExecuter {
    public function getName($nameA, $nameB) {
        return $nameA . "_or_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA == "true" || $valueB == "true") {
            return "true";
        } else {
            return (($valueA === null) || ($valueB === null) ? null : "false");
        }
    }
}

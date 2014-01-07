<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* >= */
class BinaryFunctionLargerEqualExecuter extends BinaryFunctionExecuter {
    public function getName($nameA, $nameB) {
        return $nameA . "_islargerorequal_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA === null || $valueB === null || ($valueA == "null" && is_numeric($valueB)))
            return null;
        return ($valueA >= $valueB ? "true" : "false");
    }
}

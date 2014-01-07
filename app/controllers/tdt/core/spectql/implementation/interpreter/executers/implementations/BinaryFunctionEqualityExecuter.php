<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* equality */
class BinaryFunctionEqualityExecuter extends BinaryFunctionExecuter {
    public function getName($nameA, $nameB) {
        return $nameA . "_isequal_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA === null || $valueB === null)
            return null;
        return ($valueA == $valueB ? "true" : "false");
    }
}

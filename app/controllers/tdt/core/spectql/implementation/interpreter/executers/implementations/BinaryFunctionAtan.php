<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* atan2 */
class BinaryFunctionAtan2Executer extends BinaryFunctionExecuter {
    public function getName($nameA, $nameB) {
        return "_atan2_" . $nameA . "_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA === null || $valueB === null)
            return null;
        return "" . atan2($valueA, $valueB);
    }
}

<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* log */
class BinaryFunctionLogExecuter extends BinaryFunctionExecuter {
    public function getName($nameA, $nameB) {
        return "_log_" . $nameA . "_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA === null || $valueB === null)
            return null;
        return "" . log($valueA, $valueB);
    }
}

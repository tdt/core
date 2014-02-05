<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\TernaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* substring / MID */
class TernaryFunctionSubstringExecuter extends TernaryFunctionExecuter {
    public function getName($nameA, $nameB, $nameC) {
        return "substring_" . $nameA . "_" . $nameB . "_" . $nameC;
    }
    public function doTernaryFunction($valueA, $valueB, $valueC) {
        if ($valueA === null || $valueB === null || $valueC === null)
            return null;
        return substr($valueA, $valueB, $valueC);
    }
}

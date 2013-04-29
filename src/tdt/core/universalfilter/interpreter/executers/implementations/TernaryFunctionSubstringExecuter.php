<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\TernaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

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

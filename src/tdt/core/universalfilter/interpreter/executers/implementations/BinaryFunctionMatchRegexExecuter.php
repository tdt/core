<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

/* match regex */
class BinaryFunctionMatchRegexExecuter extends BinaryFunctionExecuter {
    public function getName($nameA, $nameB) {
        return $nameA . "_matches_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA === null || $valueB === null)
            return null;
        return (preg_match($valueB, $valueA) ? "true" : "false");
    }
}

<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

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

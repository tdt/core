<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

/* concat */
class BinaryFunctionConcatExecuter extends BinaryFunctionExecuter {
    public function getName($nameA, $nameB) {
        return "_concat_" . $nameA . "_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA === null || $valueB === null)
            return null;
        return "" . $valueA . "" . $valueB;
    }
}

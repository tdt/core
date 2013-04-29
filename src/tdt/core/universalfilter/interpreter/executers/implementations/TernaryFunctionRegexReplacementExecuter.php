<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\TernaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

/* regex replace */
class TernaryFunctionRegexReplacementExecuter extends TernaryFunctionExecuter {
    public function getName($nameA, $nameB, $nameC) {
        return $nameA . "_replaced_" . $nameB . "_with_" . $nameC;
    }
    public function doTernaryFunction($valueA, $valueB, $valueC) {
        if ($valueA === null || $valueB === null || $valueC === null)
            return null;
        return preg_replace($valueA, $valueB, $valueC);
    }
}

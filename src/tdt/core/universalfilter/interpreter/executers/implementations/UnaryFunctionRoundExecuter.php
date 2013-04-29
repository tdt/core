<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

/* round */
class UnaryFunctionRoundExecuter extends UnaryFunctionExecuter {
    public function getName($name) {
        return "round_" . $name;
    }
    public function doUnaryFunction($value) {
        if ($value === null)
            return null;
        return round($value);
    }
}

<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

/* upercase */
class UnaryFunctionUppercaseExecuter extends UnaryFunctionExecuter {
    public function getName($name) {
        return "uppercase_" . $name;
    }
    public function doUnaryFunction($value) {
        if ($value === null)
            return null;
        return strtoupper($value);
    }
}

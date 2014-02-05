<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* not */
class UnaryFunctionNotExecuter extends UnaryFunctionExecuter {
    public function getName($name) {
        return "not_" . $name;
    }
    public function doUnaryFunction($value) {
        if ($value === null)
            return null;
        return ($value == "true" || $value == 1 ? "false" : "true");
    }
}

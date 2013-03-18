<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

/* isnull */
class UnaryFunctionIsNullExecuter extends UnaryFunctionExecuter {
    public function getName($name) {
        return "isnull_" . $name;
    }
    public function doUnaryFunction($value) {
        return (is_null($value) ? "true" : "false");
    }
}

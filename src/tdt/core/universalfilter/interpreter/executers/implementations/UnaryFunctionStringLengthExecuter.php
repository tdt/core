<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

/* stringlength */
class UnaryFunctionStringLengthExecuter extends UnaryFunctionExecuter {
    public function getName($name) {
        return "length_" . $name;
    }
    public function doUnaryFunction($value) {
        if ($value === null)
            return null;
        return strlen($value);
    }
}

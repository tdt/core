<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;

/* lowercase */
class UnaryFunctionLowercaseExecuter extends UnaryFunctionExecuter {
    public function getName($name) {
        return "lowercase_" . $name;
    }
    public function doUnaryFunction($value) {
        if ($value === null)
            return null;
        return strtolower($value);
    }
}

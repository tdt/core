<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* tan */
class UnaryFunctionTanExecuter extends UnaryFunctionExecuter {
    public function getName($name) {
        return "tan_" . $name;
    }
    public function doUnaryFunction($value) {
        if ($value === null)
            return null;
        return "" . tan($value);
    }
}

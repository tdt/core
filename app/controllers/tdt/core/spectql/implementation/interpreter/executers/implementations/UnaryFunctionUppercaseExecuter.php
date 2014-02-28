<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;

/* Uppercase */
class UnaryFunctionUppercaseExecuter extends UnaryFunctionExecuter {

    public function getName($name) {
        return "ucase_" . $name;
    }

    public function doUnaryFunction($value) {
        if ($value === null)
            return null;
        return strtoupper($value);
    }
}

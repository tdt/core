<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

class UnaryFunctionStringLengthExecuter extends UnaryFunctionExecuter
{

    public function getName($name) {
        return "len_" . $name;
    }

    public function doUnaryFunction($value) {

        if ($value === null || is_object($value) || is_array($value))
            return null;
        return strlen($value);
    }
}

<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* round */
class UnaryFunctionRoundExecuter extends UnaryFunctionExecuter
{
    public function getName($name)
    {
        return "round_" . $name;
    }
    public function doUnaryFunction($value)
    {
        if ($value === null)
            return null;
        return round($value);
    }
}

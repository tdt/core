<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* not */
class UnaryFunctionNotExecuter extends UnaryFunctionExecuter
{
    public function getName($name)
    {
        return "not_" . $name;
    }
    public function doUnaryFunction($value)
    {
        if ($value === null)
            return null;
        return ($value == "true" || $value == 1 ? "false" : "true");
    }
}

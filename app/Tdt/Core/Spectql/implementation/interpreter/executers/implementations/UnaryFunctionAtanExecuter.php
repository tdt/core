<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* atan */
class UnaryFunctionAtanExecuter extends UnaryFunctionExecuter
{
    public function getName($name)
    {
        return "atan_" . $name;
    }
    public function doUnaryFunction($value)
    {
        if ($value === null)
            return null;
        return "" . atan($value);
    }
}

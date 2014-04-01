<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* abs */
class UnaryFunctionAbsExecuter extends UnaryFunctionExecuter
{
    public function getName($name)
    {
        return "abs_" . $name;
    }
    public function doUnaryFunction($value)
    {
        if ($value === null)
            return null;
        return "" . abs($value);
    }
}

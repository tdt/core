<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* asin */
class UnaryFunctionAsinExecuter extends UnaryFunctionExecuter
{
    public function getName($name)
    {
        return "asin_" . $name;
    }
    public function doUnaryFunction($value)
    {
        if ($value === null)
            return null;
        return "" . asin($value);
    }
}

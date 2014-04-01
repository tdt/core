<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* isnull */
class UnaryFunctionIsNullExecuter extends UnaryFunctionExecuter
{

    public function getName($name)
    {
        return "isnull_" . $name;
    }

    public function doUnaryFunction($value)
    {
        return (is_null($value) ? "true" : "false");
    }
}

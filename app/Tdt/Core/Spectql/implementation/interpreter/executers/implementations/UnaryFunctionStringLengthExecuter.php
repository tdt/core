<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

class UnaryFunctionStringLengthExecuter extends UnaryFunctionExecuter
{

    public function getName($name)
    {
        return "len_" . $name;
    }

    public function doUnaryFunction($value)
    {

        if ($value === null || is_object($value) || is_array($value))
            return null;
        return strlen($value);
    }
}

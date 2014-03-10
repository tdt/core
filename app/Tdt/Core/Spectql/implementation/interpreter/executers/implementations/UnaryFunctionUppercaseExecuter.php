<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;

/* Uppercase */
class UnaryFunctionUppercaseExecuter extends UnaryFunctionExecuter
{

    public function getName($name)
    {
        return "ucase_" . $name;
    }

    public function doUnaryFunction($value)
    {
        if ($value === null)
            return null;
        return strtoupper($value);
    }
}

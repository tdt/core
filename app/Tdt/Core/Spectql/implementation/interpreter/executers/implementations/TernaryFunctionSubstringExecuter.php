<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\TernaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* substring / MID */
class TernaryFunctionSubstringExecuter extends TernaryFunctionExecuter
{
    public function getName($nameA, $nameB, $nameC)
    {
        return "substring_" . $nameA . "_" . $nameB . "_" . $nameC;
    }
    public function doTernaryFunction($valueA, $valueB, $valueC)
    {
        if ($valueA === null || $valueB === null || $valueC === null)
            return null;
        return substr($valueA, $valueB, $valueC);
    }
}

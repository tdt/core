<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\TernaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;

/* regex replace */
class TernaryFunctionRegexReplacementExecuter extends TernaryFunctionExecuter
{
    public function getName($nameA, $nameB, $nameC)
    {
        return $nameA . "_replaced_" . $nameB . "_with_" . $nameC;
    }
    public function doTernaryFunction($valueA, $valueB, $valueC)
    {
        if ($valueA === null || $valueB === null || $valueC === null)
            return null;
        return preg_replace($valueA, $valueB, $valueC);
    }
}

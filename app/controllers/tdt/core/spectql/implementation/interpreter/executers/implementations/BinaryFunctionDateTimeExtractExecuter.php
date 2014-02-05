<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* extract */
class BinaryFunctionDateTimeExtractExecuter extends BinaryFunctionExecuter {
    public function getName($nameA, $nameB) {
        return "_extract_" . $nameB . "_from_" . $nameA;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA === null || $valueB === null)
            return null;
        $dateTime = ExecuterDateTimeTools::getDateTime($valueA, "extract");
        return ExecuterDateTimeTools::extract($dateTime, $valueB);
    }
}

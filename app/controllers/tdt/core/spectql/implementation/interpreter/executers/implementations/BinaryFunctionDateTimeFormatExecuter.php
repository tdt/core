<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/* format */
class BinaryFunctionDateTimeFormatExecuter extends BinaryFunctionExecuter
{
    public function getName($nameA, $nameB) {
        return "_format_date_" . $nameA . "_as_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA === null || $valueB === null)
            return null;
        $dateTime = ExecuterDateTimeTools::getDateTime($valueA, "date_format");
        $formatted = $dateTime->format($valueB);
        if ($formatted === FALSE) {
            throw new Exception("Unknown format in DATE_FORMAT : " . $valueB . " Please use the php-syntax, see http://www.php.net/manual/en/function.date.php .");
        } else {
            return $formatted;
        }
    }
}

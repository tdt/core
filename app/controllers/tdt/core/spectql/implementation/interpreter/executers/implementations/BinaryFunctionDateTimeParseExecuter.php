<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;

/*
 * DateTime
 */
/* parseDateTime */
class BinaryFunctionDateTimeParseExecuter extends BinaryFunctionExecuter
{
    public function getName($nameA, $nameB) {
        return "_parseDate_" . $nameA . "_in_format_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA === null || $valueB === null)
            return null;
        $dateTime = DateTime::createFromFormat($valueB, $valueA);
        if ($dateTime === FALSE) {
            throw new Exception("Unknown format in PARSE_DATE: " . $valueB . " Please use the php-syntax, see http://www.php.net/manual/en/datetime.createfromformat.php .");
        }
        return $dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}

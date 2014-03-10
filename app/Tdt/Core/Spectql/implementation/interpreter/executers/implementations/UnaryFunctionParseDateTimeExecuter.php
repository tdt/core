<?php
namespace Tdt\Core\universalfilter;
use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\UnaryFunctionExecuter;
use Tdt\Core\Spectql\implementation\interpreter\executers\tools\ExecuterDateTimeTools;
use Tdt\Core\Spectql\implementation\interpreter\UniversalInterpreter;
/* parse_datetime */
class UnaryFunctionParseDateTimeExecuter extends UnaryFunctionExecuter
{
    public function getName($name)
    {
        return "parse_datetime_" . $name;
    }
    public function doUnaryFunction($value)
    {
        if ($value === null)
            return null;
        $dateTime = new DateTime($value);
        return $dateTime->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}

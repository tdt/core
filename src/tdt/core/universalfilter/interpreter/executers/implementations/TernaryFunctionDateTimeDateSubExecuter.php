<?php
namespace tdt\core\universalfilter\interpreter\executers\implementations;
use tdt\core\universalfilter\interpreter\executers\implementations\TernaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;
/* date sub */
class TernaryFunctionDateTimeDateSubExecuter extends TernaryFunctionExecuter {
    public function getName($nameA, $nameB, $nameC) {
        return "_date_sub_" . $nameA . "_interval_" . $nameB . "_" . $nameC;
    }
    public function doTernaryFunction($valueA, $valueB, $valueC) {
        if ($valueA === null || $valueB === null || $valueC === null)
            return null;
        $dateTime = ExecuterDateTimeTools::getDateTime($valueA, "date_sub");
        $interval = ExecuterDateTimeTools::toInterval($valueB, $valueC);
        return $dateTime->sub($interval)->format(UniversalInterpreter::$INTERNAL_DATETIME_FORMAT);
    }
}
?>

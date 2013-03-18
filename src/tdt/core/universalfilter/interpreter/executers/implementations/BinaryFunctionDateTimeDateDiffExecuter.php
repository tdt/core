<?php
namespace tdt\core\universalfilter;
use tdt\core\universalfilter\interpreter\executers\implementations\BinaryFunctionExecuter;
use tdt\core\universalfilter\interpreter\executers\tools\ExecuterDateTimeTools;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;
/* datediff */
class BinaryFunctionDateTimeDateDiffExecuter extends BinaryFunctionExecuter {
    public function getName($nameA, $nameB) {
        return "_datediff_" . $nameA . "_and_" . $nameB;
    }
    public function doBinaryFunction($valueA, $valueB) {
        if ($valueA === null || $valueB === null)
            return null;
        $dateTimeA = ExecuterDateTimeTools::getDateTime($valueA, "datediff");
        $dateTimeB = ExecuterDateTimeTools::getDateTime($valueB, "datediff");
        $dateTimeA->setTime(0, 0, 0);
        $dateTimeB->setTime(0, 0, 0);
        $interval = $dateTimeB->diff($dateTimeA);
        return ($interval->invert ? "-" : "") . $interval->days;
    }
}
?>

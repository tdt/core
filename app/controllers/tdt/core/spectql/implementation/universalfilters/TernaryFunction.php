<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * This class represents all ternary functions
 *
 * type: (Column, Column, Column) -> Column
 * type: (Cell, Cell, Cell) -> Cell
 */
class TernaryFunction extends NormalFilterNode
{

    public static $FUNCTION_TERNARY_SUBSTRING = "FUNCTION_TERNARY_SUBSTRING"; //get part of $1 from index $2 with length $3
    public static $FUNCTION_TERNARY_REGEX_REPLACE = "FUNCTION_TERNARY_REGEX_REPLACE"; //replace $1 by $2 in $3
    public static $FUNCTION_TERNARY_DATETIME_DATEADD = "FUNCTION_TERNARY_DATETIME_DATEADD"; // (date, string, constant:DateTimeExtractConstant)  (DATE_ADD(date INTERVAL string constant))
    public static $FUNCTION_TERNARY_DATETIME_DATESUB = "FUNCTION_TERNARY_DATETIME_DATESUB"; // (date, string, constant:DateTimeExtractConstant)  (DATE_SUB(date INTERVAL string constant))

    public function __construct($kind, UniversalFilterNode $columnA = null, UniversalFilterNode $columnB = null, UniversalFilterNode $columnC = null)
    {
        parent::__construct($kind);
        if ($columnA != null)
            $this->setSource($columnA, 0);
        if ($columnB != null)
            $this->setSource($columnB, 1);
        if ($columnC != null)
            $this->setSource($columnC, 2);
    }

    public function getSourceCount()
    {
        return 3;
    }
}

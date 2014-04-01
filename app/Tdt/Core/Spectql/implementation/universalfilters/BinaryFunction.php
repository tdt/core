<?php

namespace Tdt\Core\Spectql\implementation\Universalfilters;

use Tdt\Core\Spectql\implementation\universalfilters\CheckInFunction;
use Tdt\Core\Spectql\implementation\universalfilters\Identifier;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * This class represents all binary functions
 *
 * type: (Column, Column) -> Column
 * type: (Cell, Cell) -> Cell
 */
class BinaryFunction extends NormalFilterNode
{

    public static $FUNCTION_BINARY_PLUS = "FUNCTION_BINARY_PLUS";
    public static $FUNCTION_BINARY_MINUS = "FUNCTION_BINARY_MINUS";
    public static $FUNCTION_BINARY_MULTIPLY = "FUNCTION_BINARY_MULTIPLY";
    public static $FUNCTION_BINARY_DIVIDE = "FUNCTION_BINARY_DIVIDE";
    public static $FUNCTION_BINARY_COMPARE_EQUAL = "FUNCTION_BINARY_COMPARE_EQUAL";
    public static $FUNCTION_BINARY_COMPARE_SMALLER_THAN = "FUNCTION_BINARY_COMPARE_SMALLER_THAN";
    public static $FUNCTION_BINARY_COMPARE_LARGER_THAN = "FUNCTION_BINARY_COMPARE_LARGER_THAN";
    public static $FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN = "FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN";
    public static $FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN = "FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN";
    public static $FUNCTION_BINARY_COMPARE_NOTEQUAL = "FUNCTION_BINARY_COMPARE_NOTEQUAL";
    public static $FUNCTION_BINARY_OR = "FUNCTION_BINARY_OR";
    public static $FUNCTION_BINARY_AND = "FUNCTION_BINARY_AND";
    public static $FUNCTION_BINARY_MATCH_REGEX = "FUNCTION_BINARY_MATCH_REGEX"; // does $1 matches $2 ? ($2 is in php regex format!)
    public static $FUNCTION_BINARY_ATAN2 = "FUNCTION_BINARY_ATAN2";
    public static $FUNCTION_BINARY_LOG = "FUNCTION_BINARY_LOG";
    public static $FUNCTION_BINARY_POW = "FUNCTION_BINARY_POW";
    public static $FUNCTION_BINARY_CONCAT = "FUNCTION_BINARY_CONCAT";
    public static $FUNCTION_BINARY_DATETIME_PARSE = "FUNCTION_BINARY_DATETIME_PARSE"; //time, php format
    public static $FUNCTION_BINARY_DATETIME_EXTRACT = "FUNCTION_BINARY_DATETIME_EXTRACT"; /* time, DateTimeExtractConstants */
    public static $FUNCTION_BINARY_DATETIME_FORMAT = "FUNCTION_BINARY_DATETIME_FORMAT"; /* time, php format */
    public static $FUNCTION_BINARY_DATETIME_DATEDIFF = "FUNCTION_BINARY_DATETIME_DATEDIFF";

    public function __construct($kind, UniversalFilterNode $columnA = null, UniversalFilterNode $columnB = null)
    {
        parent::__construct($kind);
        if ($columnA != null)
            $this->setSource($columnA, 0);
        if ($columnB != null)
            $this->setSource($columnB, 1);
    }

    public function getSourceCount()
    {
        return 2;
    }
}

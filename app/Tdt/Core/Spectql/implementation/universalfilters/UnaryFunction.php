<?php

namespace Tdt\Core\Spectql\implementation\Universalfilters;

use Tdt\Core\Spectql\implementation\universalfilters\CheckInFunction;
use Tdt\Core\Spectql\implementation\universalfilters\Identifier;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * This class represents all unary functions
 *
 * type: Column -> Column
 * type: Cell -> Cell
 */
class UnaryFunction extends NormalFilterNode
{

    public static $FUNCTION_UNARY_UPPERCASE = "FUNCTION_UNARY_UPPERCASE";
    public static $FUNCTION_UNARY_LOWERCASE = "FUNCTION_UNARY_LOWERCASE";
    public static $FUNCTION_UNARY_STRINGLENGTH = "FUNCTION_UNARY_STRINGLENGTH";
    public static $FUNCTION_UNARY_ROUND = "FUNCTION_UNARY_ROUND";
    public static $FUNCTION_UNARY_ISNULL = "FUNCTION_UNARY_ISNULL";
    public static $FUNCTION_UNARY_NOT = "FUNCTION_UNARY_NOT";
    public static $FUNCTION_UNARY_SIN = "FUNCTION_UNARY_SIN";
    public static $FUNCTION_UNARY_COS = "FUNCTION_UNARY_COS";
    public static $FUNCTION_UNARY_TAN = "FUNCTION_UNARY_TAN";
    public static $FUNCTION_UNARY_ASIN = "FUNCTION_UNARY_ASIN";
    public static $FUNCTION_UNARY_ACOS = "FUNCTION_UNARY_ACOS";
    public static $FUNCTION_UNARY_ATAN = "FUNCTION_UNARY_ATAN";
    public static $FUNCTION_UNARY_SQRT = "FUNCTION_UNARY_SQRT";
    public static $FUNCTION_UNARY_ABS = "FUNCTION_UNARY_ABS";
    public static $FUNCTION_UNARY_FLOOR = "FUNCTION_UNARY_FLOOR";
    public static $FUNCTION_UNARY_CEIL = "FUNCTION_UNARY_CEIL";
    public static $FUNCTION_UNARY_EXP = "FUNCTION_BINARY_EXP";
    public static $FUNCTION_UNARY_LOG = "FUNCTION_BINARY_LOG";
    public static $FUNCTION_UNARY_DATETIME_PARSE = "FUNCTION_UNARY_DATETIME_PARSE";
    public static $FUNCTION_UNARY_DATETIME_DATEPART = "FUNCTION_UNARY_DATETIME_DATEPART";

    public function __construct($kind, UniversalFilterNode $column = null)
    {
        parent::__construct($kind);
        if ($column != null)
            $this->setSource($column, 0);
    }
}

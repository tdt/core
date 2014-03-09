<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Represents a filter that keeps the row if expression results in true
 * expression is a filter too.
 *
 * type: Table -> Table
 * type: GroupedTable -> GroupedTable
 *
 * aka "WHERE" or "HAVING"
 */
class FilterByExpressionFilter extends NormalFilterNode
{

    private $expression; //type:UniversalFilterNode

    public function __construct(UniversalFilterNode $expression, UniversalFilterNode $source = null)
    {
        parent::__construct("FILTEREXPRESSION");
        $this->expression = $expression;
        if ($source != null)
            $this->setSource($source);
    }

    public function getExpression()
    {
        return $this->expression;
    }

}


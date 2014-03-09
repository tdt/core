<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/** Represents a column used in the ColumnSelectionFilter */
class ColumnSelectionFilterColumn
{

    private $column; //type:UniversalFilterNode
    private $alias; //type:String (can be null)

    public function __construct(UniversalFilterNode $column, $alias = null)
    {
        $this->column = $column;
        $this->alias = $alias;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function getAlias()
    {
        return $this->alias;
    }
}

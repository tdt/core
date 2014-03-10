<?php

namespace Tdt\Core\Spectql\implementation\Universalfilters;

use Tdt\Core\Spectql\implementation\universalfilters\CheckInFunction;
use Tdt\Core\Spectql\implementation\universalfilters\Identifier;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

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

<?php

namespace tdt\core\universalfilter\Universalfilters;

use tdt\core\universalfilter\universalfilters\CheckInFunction;
use tdt\core\universalfilter\universalfilters\Identifier;
use tdt\core\universalfilter\universalfilters\NormalFilterNode;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

/** Represents a column used in the ColumnSelectionFilter */
class ColumnSelectionFilterColumn {

    private $column; //type:UniversalFilterNode
    private $alias; //type:String (can be null)

    public function __construct(UniversalFilterNode $column, $alias = null) {
        $this->column = $column;
        $this->alias = $alias;
    }

    public function getColumn() {
        return $this->column;
    }

    public function getAlias() {
        return $this->alias;
    }

}


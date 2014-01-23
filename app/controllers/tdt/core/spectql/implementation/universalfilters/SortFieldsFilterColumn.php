<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Represents a column used in the SortFieldsFilter
 */
class SortFieldsFilterColumn {

    private $column; //type:Identifier
    private $sortorder; //type:boolean
    public static $SORTORDER_ASCENDING = true;
    public static $SORTORDER_DESCENDING = false;

    public function __construct(Identifier $column, $sortorder = true) {
        $this->column = $column;
        $this->sortorder = $sortorder;
    }

    public function getColumn() {
        return $this->column;
    }

    public function getSortOrder() {
        return $this->sortorder;
    }

}


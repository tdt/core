<?php

namespace tdt\core\universalfilter\Universalfilters;

use tdt\core\universalfilter\universalfilters\CheckInFunction;
use tdt\core\universalfilter\universalfilters\Identifier;
use tdt\core\universalfilter\universalfilters\NormalFilterNode;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

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


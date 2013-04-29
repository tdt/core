<?php

namespace tdt\core\universalfilter\Universalfilters;

use tdt\core\universalfilter\universalfilters\CheckInFunction;
use tdt\core\universalfilter\universalfilters\Identifier;
use tdt\core\universalfilter\universalfilters\NormalFilterNode;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

/**
 * Represents a filter that sorts the columns given in $columndata.
 *
 * type: Table -> Table
 *
 * aka "ORDER BY"
 */
class SortFieldsFilter extends NormalFilterNode {

    private $columndata; //type:Array[SortFieldsFilterColumn]

    public function __construct(array /* of SortFieldsFilterColumn */ $columndata, UniversalFilterNode $source = null) {
        parent::__construct("FILTERSORTCOLUMNS");
        $this->columndata = $columndata;
        if ($source != null)
            $this->setSource($source);
    }

    public function getColumnData() {
        return $this->columndata;
    }

}


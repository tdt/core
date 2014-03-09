<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Represents a filter that sorts the columns given in $columndata.
 *
 * type: Table -> Table
 *
 * aka "ORDER BY"
 */
class SortFieldsFilter extends NormalFilterNode
{

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


<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Represents a filter that filters columns by applying the filters in $columndata
 *
 * The resulting table is never grouped!
 *
 * Table aliases will be removed when executing this filter
 *
 * type: Table -> Table
 * type: GroupedTable -> Table
 *
 * aka "SELECT"
 */
class ColumnSelectionFilter extends NormalFilterNode
{

    private $columndata; //type:Array[ColumnSelectionFilterColumn]

    public function __construct(array /* of ColumnSelectionFilterColumn */ $columndata, UniversalFilterNode $source = null)
    {
        parent::__construct("FILTERCOLUMN");
        $this->columndata = $columndata;
        if ($source != null)
            $this->setSource($source);
    }

    public function getColumnData()
    {
        return $this->columndata;
    }
}

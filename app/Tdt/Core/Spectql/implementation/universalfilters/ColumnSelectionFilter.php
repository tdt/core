<?php

namespace Tdt\Core\Spectql\implementation\Universalfilters;

use Tdt\Core\Spectql\implementation\universalfilters\CheckInFunction;
use Tdt\Core\Spectql\implementation\universalfilters\Identifier;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

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

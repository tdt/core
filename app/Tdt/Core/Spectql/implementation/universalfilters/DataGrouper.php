<?php

namespace Tdt\Core\Spectql\implementation\Universalfilters;

use Tdt\Core\Spectql\implementation\universalfilters\CheckInFunction;
use Tdt\Core\Spectql\implementation\universalfilters\Identifier;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Groups the data (not really a filter)
 * When the data is grouped, it can not be grouped again.
 * Futhermore it can be filtered only by a select number of filters:
 *  - FilterByExpression
 *  - ColumnSelectionFilter (after this node the data is ungrouped again)
 *
 * type: Table -> GroupedTable
 *
 * aka "GROUP BY"
 */
class DataGrouper extends NormalFilterNode
{

    private $columns;

    public function __construct(array $columns, UniversalFilterNode $source = null)
    {

        parent::__construct("DATAGROUPER");

        $this->columns = $columns;
        if ($source != null)
            $this->setSource($source);
    }

    public function getColumns()
    {
        return $this->columns;
    }
}

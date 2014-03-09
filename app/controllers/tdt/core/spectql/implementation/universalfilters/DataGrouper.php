<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

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

    public function __construct(array $columns, UniversalFilterNode $source = null) {

        parent::__construct("DATAGROUPER");

        $this->columns = $columns;
        if ($source != null)
            $this->setSource($source);
    }

    public function getColumns() {
        return $this->columns;
    }

}


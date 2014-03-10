<?php

namespace Tdt\Core\Spectql\implementation\Universalfilters;

use Tdt\Core\Spectql\implementation\universalfilters\CheckInFunction;
use Tdt\Core\Spectql\implementation\universalfilters\Identifier;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

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

    public function __construct(array /* of SortFieldsFilterColumn */ $columndata, UniversalFilterNode $source = null)
    {
        parent::__construct("FILTERSORTCOLUMNS");
        $this->columndata = $columndata;
        if ($source != null)
            $this->setSource($source);
    }

    public function getColumnData()
    {
        return $this->columndata;
    }
}

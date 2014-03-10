<?php

namespace Tdt\Core\Spectql\implementation\Universalfilters;

use Tdt\Core\Spectql\implementation\universalfilters\CheckInFunction;
use Tdt\Core\Spectql\implementation\universalfilters\Identifier;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Represents a column used in the SortFieldsFilter
 */
class SortFieldsFilterColumn
{

    private $column; //type:Identifier
    private $sortorder; //type:boolean
    public static $SORTORDER_ASCENDING = true;
    public static $SORTORDER_DESCENDING = false;

    public function __construct(Identifier $column, $sortorder = true)
    {
        $this->column = $column;
        $this->sortorder = $sortorder;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function getSortOrder()
    {
        return $this->sortorder;
    }
}

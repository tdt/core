<?php

/**
 * This file represents a part of the UniversalFilterTree that is calculated externally.
 *
 * @package The-Datatank/universalfilter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace Tdt\Core\Spectql\implementation\sourcefilterbinding;

use Tdt\Core\Spectql\implementation\data\UniversalFilterTable;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

class ExternallyCalculatedFilterNode extends NormalFilterNode
{

    private $table;

    public function __construct(UniversalFilterTable $table, UniversalFilterNode $implementedFilter)
    {
        parent::__construct("EXTERNALLY_CALCULATED_NODE");
        $this->table = $table;
        if ($implementedFilter != null)
            $this->setSource($implementedFilter);
    }

    public function getTable()
    {
        return $this->table;
    }
}

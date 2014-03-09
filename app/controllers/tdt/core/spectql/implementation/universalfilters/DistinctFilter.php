<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Represents a distinct filter => keeps only the rows that are distinct
 *
 * type: Table -> Table
 *
 * aka "DISTINCT"
 */
class DistinctFilter extends NormalFilterNode
{

    public function __construct(UniversalFilterNode $source = null)
    {
        parent::__construct("FILTERDISTINCT");
        if ($source != null)
            $this->setSource($source);
    }

}


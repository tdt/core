<?php

namespace tdt\core\universalfilter\Universalfilters;

use tdt\core\universalfilter\universalfilters\CheckInFunction;
use tdt\core\universalfilter\universalfilters\Identifier;
use tdt\core\universalfilter\universalfilters\NormalFilterNode;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

/**
 * Represents a distinct filter => keeps only the rows that are distinct
 *
 * type: Table -> Table
 *
 * aka "DISTINCT"
 */
class DistinctFilter extends NormalFilterNode {

    public function __construct(UniversalFilterNode $source = null) {
        parent::__construct("FILTERDISTINCT");
        if ($source != null)
            $this->setSource($source);
    }

}


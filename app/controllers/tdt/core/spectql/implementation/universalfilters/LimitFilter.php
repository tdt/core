<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Represents a limit filter => keeps a certain amount of rows from a certain offset
 * Note that rows start counting from 0 thus limit(0,10) will return the first 10 rows.
 *
 * type: Table -> Table
 *
 * aka "LIMIT"
 */
class LimitFilter extends NormalFilterNode {

    public function __construct(UniversalFilterNode $source = null, $offset, $limit) {
        parent::__construct("FILTERLIMIT");
        if ($source != null)
            $this->setSource($source);
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function setOffset($offset) {
        $this->offset = $offset;
    }

    public function getOffset() {
        return $this->offset;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
    }

    public function getLimit() {
        return $this->limit;
    }

}


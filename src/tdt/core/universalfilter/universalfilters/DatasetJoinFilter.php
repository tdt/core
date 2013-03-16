<?php

namespace tdt\core\universalfilter\Universalfilters;

use tdt\core\universalfilter\universalfilters\CheckInFunction;
use tdt\core\universalfilter\universalfilters\Identifier;
use tdt\core\universalfilter\universalfilters\NormalFilterNode;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

/**
 * Represents a filter that joins dataSets...
 *
 * type: Table -> Table
 *
 * aka "INNER JOIN"
 * aka "LEFT OUTER JOIN"
 * aka "RIGHT OUTER JOIN"
 * aka "FULL OUTER JOIN"
 * aka "CROSS JOIN"
 */
class DatasetJoinFilter extends NormalFilterNode {

    private $expression; //type:UniversalFilterNode
    private $keepleft; //type:boolean
    private $keepright; //type:boolean

    public function __construct($keepleft = false, $keepright = false, UniversalFilterNode $sourceA = null, UniversalFilterNode $sourceB = null, UniversalFilterNode $expression = null) {
        parent::__construct("JOIN");
        $this->expression = $expression;
        $this->keepleft = $keepleft;
        $this->keepright = $keepright;
        if ($sourceA != null)
            $this->setSource($sourceA, 0);
        if ($sourceB != null)
            $this->setSource($sourceB, 1);
    }

    public function getExpression() {
        return $this->expression;
    }

    public function getSourceCount() {
        return 2;
    }

    public function getKeepLeft() {
        return $this->keepleft;
    }

    public function getKeepRight() {
        return $this->keepright;
    }

}

/*
 *
 *  --- FUNCTIONS ---
 *
 */


<?php

namespace tdt\core\universalfilter\Universalfilters;

use tdt\core\universalfilter\universalfilters\CheckInFunction;
use tdt\core\universalfilter\universalfilters\Identifier;
use tdt\core\universalfilter\universalfilters\NormalFilterNode;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

/**
 * Represents a constant
 * Can be a string, a boolean, or a number.
 */
class Constant extends UniversalFilterNode {

    private $constant; //type:String

    public function __construct($constant) {
        parent::__construct("CONSTANT");
        $this->constant = $constant;
    }

    public function getConstant() {
        return $this->constant;
    }

}


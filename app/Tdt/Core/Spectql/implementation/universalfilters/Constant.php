<?php

namespace Tdt\Core\Spectql\implementation\Universalfilters;

use Tdt\Core\Spectql\implementation\universalfilters\CheckInFunction;
use Tdt\Core\Spectql\implementation\universalfilters\Identifier;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Represents a constant
 * Can be a string, a boolean, or a number.
 */
class Constant extends UniversalFilterNode
{

    private $constant; //type:String

    public function __construct($constant)
    {
        parent::__construct("CONSTANT");
        $this->constant = $constant;
    }

    public function getConstant()
    {
        return $this->constant;
    }
}

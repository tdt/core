<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Represents a table alias
 * Has a source and a alias string
 */
class TableAliasFilter extends NormalFilterNode {

    private $alias; //type:String

    public function __construct($alias, UniversalFilterNode $source = null) {
        parent::__construct("TABLEALIAS");
        $this->alias = $alias;
        if ($source != null)
            $this->setSource($source);
    }

    public function getAlias() {
        return $this->alias;
    }

}


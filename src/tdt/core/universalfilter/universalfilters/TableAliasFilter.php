<?php

namespace tdt\core\universalfilter\Universalfilters;

use tdt\core\universalfilter\universalfilters\CheckInFunction;
use tdt\core\universalfilter\universalfilters\Identifier;
use tdt\core\universalfilter\universalfilters\NormalFilterNode;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

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


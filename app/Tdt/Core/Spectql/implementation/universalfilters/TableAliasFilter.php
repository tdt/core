<?php

namespace Tdt\Core\Spectql\implementation\Universalfilters;

use Tdt\Core\Spectql\implementation\universalfilters\CheckInFunction;
use Tdt\Core\Spectql\implementation\universalfilters\Identifier;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Represents a table alias
 * Has a source and a alias string
 */
class TableAliasFilter extends NormalFilterNode
{

    private $alias; //type:String

    public function __construct($alias, UniversalFilterNode $source = null)
    {
        parent::__construct("TABLEALIAS");
        $this->alias = $alias;
        if ($source != null)
            $this->setSource($source);
    }

    public function getAlias()
    {
        return $this->alias;
    }
}

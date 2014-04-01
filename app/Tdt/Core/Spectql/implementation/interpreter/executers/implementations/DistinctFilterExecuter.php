<?php

/**
 * Executes the DistinctFilter filter
 *
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\data\UniversalFilterTableHeaderColumnInfo;
use Tdt\Core\Spectql\implementation\interpreter\executers\base\BaseHashingFilterExecuter;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

class DistinctFilterExecuter extends BaseHashingFilterExecuter
{

    public function hashColumn(UniversalFilterNode $filter, UniversalFilterTableHeaderColumnInfo $oldColumnInfo)
    {
        return true;
    }
}

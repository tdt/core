<?php

/**
 * Executes the DistinctFilter filter
 *
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\data\UniversalFilterTableHeaderColumnInfo;
use tdt\core\spectql\implementation\interpreter\executers\base\BaseHashingFilterExecuter;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

class DistinctFilterExecuter extends BaseHashingFilterExecuter
{

    public function hashColumn(UniversalFilterNode $filter, UniversalFilterTableHeaderColumnInfo $oldColumnInfo)
    {
        return true;
    }
}

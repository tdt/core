<?php

use tdt\core\universalfilter\data\UniversalFilterTableHeaderColumnInfo;
use tdt\core\universalfilter\interpreter\executers\base\BaseHashingFilterExecuter;
use tdt\core\universalfilter\UniversalFilterNode;

/**
 * Executes the DistinctFilter filter
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\interpreter\executers\implementations;

class DistinctFilterExecuter extends BaseHashingFilterExecuter {
    
    public function hashColumn(UniversalFilterNode $filter, UniversalFilterTableHeaderColumnInfo $oldColumnInfo){
        return true;
    }
}

?>

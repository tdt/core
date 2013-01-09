<?php

/**
 * Executes the DistinctFilter filter
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\interpreter\executers\implementations;

class DistinctFilterExecuter extends tdt\core\universalfilter\interpreter\executers\base\BaseHashingFilterExecuter {
    
    public function hashColumn(tdt\core\universalfilter\UniversalFilterNode $filter, tdt\core\universalfilter\data\UniversalFilterTableHeaderColumnInfo $oldColumnInfo){
        return true;
    }
}

?>

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

class DataGrouperExecuter extends tdt\core\universalfilter\interpreter\executers\base\BaseHashingFilterExecuter {
    
    public function hashColumn(tdt\core\universalfilter\UniversalFilterNode $filter, tdt\core\universalfilter\data\UniversalFilterTableHeaderColumnInfo $oldColumnInfo){        
        //get the columns to group
        $columnIdentifiers = $filter->getColumns();
        
        $needToBeGrouped=true;
        for ($columnNameIndex = 0; $columnNameIndex < count($columnIdentifiers); $columnNameIndex++) {
            $columnIdentifier = $columnIdentifiers[$columnNameIndex]->getIdentifierString();
            if($oldColumnInfo->matchName(explode(".", $columnIdentifier))){
                $needToBeGrouped=false;
            }
        }
        return !$needToBeGrouped;
    }
}

?>

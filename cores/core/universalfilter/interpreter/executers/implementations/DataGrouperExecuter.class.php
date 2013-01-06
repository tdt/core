<?php

/**
 * Executes the DistinctFilter filter
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class DataGrouperExecuter extends BaseHashingFilterExecuter {
    
    public function hashColumn(UniversalFilterNode $filter, UniversalFilterTableHeaderColumnInfo $oldColumnInfo){        
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

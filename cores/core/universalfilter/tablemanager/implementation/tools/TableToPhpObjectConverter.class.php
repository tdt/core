<?php

/**
 * This class can convert a table (as used by the interpreter) to a php-object
 *
 * @package The-Datatank/universalfilter/tablemanager/tools
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class TableToPhpObjectConverter {
    
    /**
     * Converts a table (as used by the interpreter) to a php-object
     * 
     * @param UniversalFilterTable $table
     * @param boolean $removeAdditionalField Remove the _id and _key_... fields?
     * @return array The Php-version of the table 
     */
    public function getPhpObjectForTable(UniversalFilterTable $table, $removeAdditionalField=true){
        $newRows = array();
        
        //initialize rows
        for ($index = 0; $index < $table->getContent()->getRowCount(); $index++) {
            $row = $table->getContent()->getRow($index);
            
            array_push($newRows, array());
        }
        
        //loop all columns
        for ($index = 0; $index < $table->getHeader()->getColumnCount(); $index++) {
            $id = $table->getHeader()->getColumnIdByIndex($index);
            $name = $table->getHeader()->getColumnUniqueNameById($id);
            
            // don't show grouped fields and _id and _key_... fields
            if(!$table->getHeader()->getColumnInformationById($id)->isGrouped() && !(!$removeAdditionalField || ($name=="_id" || strpos($name, "_key_")===0))){
                // loop all rows
                for ($rindex = 0; $rindex < $table->getContent()->getRowCount(); $rindex++) {
                    // and add the column to the php object
                    $row = $table->getContent()->getRow($rindex);
                    $newRows[$rindex][$name] = $row->getCellValue($id, true);// ALLOW null values!
                }
                
            }
        }
        
        return $newRows;
    }
}

?>

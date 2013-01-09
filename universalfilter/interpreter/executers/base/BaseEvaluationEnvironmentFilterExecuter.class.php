<?php

/**
 * Base class for filters that evaluate expressions AND have a source (like select and where)
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\executers\base;

abstract class BaseEvaluationEnvironmentFilterExecuter extends tdt\core\universalfilter\executers\base\AbstractUniversalFilterNodeExecuter {
    //put your code here
    
    /**
     * Build the child environment to give to children.
     * This environment combines the info from the parent environment AND the info from the source table
     * 
     * @param UniversalFilterNode $filter
     * @param Environment $topenv
     * @return array Intern data
     */
    protected function initChildEnvironment(tdt\core\universalfilter\UniversalFilterNode $filter, tdt\core\universalfilter\interpreter\Environment $topenv, tdt\core\universalfilter\interpreter\IInterpreterControl $interpreter, $preferColumn) {
        //
        // BUILD ENVIRONMENT TO GIVE TO EXPRESSIONS
        //
        
        //get source environment header
        $executer->initExpression($filter->getSource(), $topenv, $interpreter, $preferColumn);
        $header = $executer->getExpressionHeader();
        
        //create new enviroment => combine given table ($topenv) and source table (from executer)
        $giveToColumnsEnvironment = $topenv->newModifiableEnvironment();
        $oldtable = $topenv->getTable();//save old table
        $oldTableRow = new UniversalFilterTableContentRow();
        
        //build new environment
        if(!$oldtable->getHeader()->isSingleRowByConstruction()){
            throw new tdt\framework\TDTException(500,array("Illegal location for ColumnSelectionFilter or FilterByExpressionFilter"));
        }
        
        for ($oldtablecolumn = 0; $oldtablecolumn < $oldtable->getHeader()->getColumnCount(); $oldtablecolumn++) {
            $columnid = $oldtable->getHeader()->getColumnIdByIndex($oldtablecolumn);
            $column = $oldtable->getHeader()->getColumnInformationById($columnid);
            
            //$oldtable->getContent()->getRow(0)->copyValueTo($oldTableRow, $columnid, $columnid);
            
            $giveToColumnsEnvironment->addSingleValue($column, $oldTableRow);
        }
        
        $giveToColumnsEnvironment->setTable(new tdt\core\universalfilter\UniversalFilterTable($header, new tdt\core\universalfilter\UniversalFilterTableContent()));
        return array("env" => $giveToColumnsEnvironment, "row" => $oldTableRow, "table" => $oldtable);
    }
    
    protected function getChildEnvironment($data){
        return $data["env"];
    }

    protected function finishChildEnvironment($data){
        $oldtable = $data["table"];
        $giveToColumnsEnvironment = $data["env"];
        $oldTableRow = $data["row"];
        
        for ($oldtablecolumn = 0; $oldtablecolumn < $oldtable->getHeader()->getColumnCount(); $oldtablecolumn++) {
            $columnid = $oldtable->getHeader()->getColumnIdByIndex($oldtablecolumn);
            $column = $oldtable->getHeader()->getColumnInformationById($columnid);
            
            $oldtable->getContent()->getRow(0)->copyValueTo($oldTableRow, $columnid, $columnid);
        }
    }
}

?>

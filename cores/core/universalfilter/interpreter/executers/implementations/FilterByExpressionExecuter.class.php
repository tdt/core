<?php

/**
 * Executes the FilterByExpression filter
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class FilterByExpressionExecuter extends BaseEvaluationEnvironmentFilterExecuter {

    private $interpreter;
    
    private $header;
    
    private $executer;
    private $exprexec;
    
    private $childEnvironmentData;
    private $giveToColumnsEnvironment;
    
    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn) {
        $this->filter = $filter;
        $this->interpreter = $interpreter;
        
        
        
        
        //get source environment header
        $executer = $interpreter->findExecuterFor($filter->getSource());
        $this->executer = $executer;
        
        
        $this->childEnvironmentData = $this->initChildEnvironment($filter, $topenv, $interpreter, $executer, $preferColumn);
        $this->giveToColumnsEnvironment = $this->getChildEnvironment($this->childEnvironmentData);
        
        
        
        //
        // BUILD OWN HEADER
        //

        //create the new header
        //   -> It's the same as the source (we could copy it here...)
        $this->header=$this->executer->getExpressionHeader();
        
        
        
        
        // get executer for expression
        $expr = $this->filter->getExpression();
        $this->exprexec = $this->interpreter->findExecuterFor($expr);
        $this->exprexec->initExpression($expr, $this->giveToColumnsEnvironment, $this->interpreter, true);
        
    }
    
    public function getExpressionHeader() {
        return $this->header;
    }
    
    public function evaluateAsExpression() {
        $sourceheader =$this->executer->getExpressionHeader();
        $sourcecontent=$this->executer->evaluateAsExpression();
        
        $this->finishChildEnvironment($this->childEnvironmentData);
        $this->giveToColumnsEnvironment->setTable(new UniversalFilterTable($sourceheader, $sourcecontent));
        
        //get expression header
        $exprheader = $this->exprexec->getExpressionHeader();
        
        // filter the content
        $filteredRows = new UniversalFilterTableContent();
        
        
        // calcultate the table with true and false
        $inResultTable = $this->exprexec->evaluateAsExpression();
        
        //loop all rows
        for ($index = 0; $index < $sourcecontent->getRowCount(); $index++) {
            $row = $sourcecontent->getRow($index);
            
            //get the right value in the result
            $answer = null;
            if($index<$inResultTable->getRowCount()){
                $answer = $inResultTable->getRow($index);
            }else{
                $answer = $inResultTable->getRow(0);
            }
            
            //if the expression evaluates to true, then add the row
            if($answer->getCellValue($exprheader->getColumnId(), false)=="true"){
                $filteredRows->addRow($row);
            }
        }
        
        $inResultTable->tryDestroyTable();
        
        $sourcecontent->tryDestroyTable();
        
        return $filteredRows;
    }
    
    public function cleanUp(){
        $this->executer->cleanUp();
        $this->exprexec->cleanUp();
    }
    
    public function modififyFiltersWithHeaderInformation(){
        parent::modififyFiltersWithHeaderInformation();
        $this->executer->modififyFiltersWithHeaderInformation();
        $this->exprexec->modififyFiltersWithHeaderInformation();
    }
    
    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex){
        $arr=array_merge(
            $this->executer->filterSingleSourceUsages($this->filter, 0),
            $this->exprexec->filterSingleSourceUsages($this->filter, -1));//TODO: give a correct source number -> only a problem when allowing independent select in where (!) (see also readme for optimizer) (not a problem when nested selects are not allowed)
        
        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }
}

?>

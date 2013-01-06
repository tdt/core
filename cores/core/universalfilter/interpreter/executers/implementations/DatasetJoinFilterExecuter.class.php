<?php

/**
 * Executes the DatasetJoinFilterExecuter filter
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class DatasetJoinFilterExecuter extends BaseEvaluationEnvironmentFilterExecuter {
    
    private $interpreter;
    
    private $header;
    
    private $totaltable;
    
    private $executerA;
    private $executerB;
    private $exprexec;

    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn) {
        $this->filter = $filter;
        $this->interpreter = $interpreter;
        
        
        //get source environment header
        $executerA = $interpreter->findExecuterFor($filter->getSource(0));
        $executerA->initExpression($filter->getSource(0), $topenv, $interpreter, false);
        $this->executerA = $executerA;
        
        $executerB = $interpreter->findExecuterFor($filter->getSource(1));
        $executerB->initExpression($filter->getSource(1), $topenv, $interpreter, false);
        $this->executerB = $executerB;
        
        
        
        //
        // BUILD OWN HEADER
        //

        //older headers...
        $headerA=$executerA->getExpressionHeader();
        $headerB=$executerB->getExpressionHeader();
        
        //todo: merge headers...
        $columns = array();
        for ($i=0;$i < $headerA->getColumnCount(); $i++){
            array_push($columns, $headerA->getColumnInformationByIndex($i)->cloneColumnNewId());
        }
        for ($i=0;$i < $headerB->getColumnCount(); $i++){
            array_push($columns, $headerB->getColumnInformationByIndex($i)->cloneColumnNewId());
        }
        
        $this->header = new UniversalFilterTableHeader($columns, FALSE, FALSE);
        
        
        //
        // EXPRESSION
        //
        if($this->filter->getExpression()!==NULL) {
            $this->totaltable = new UniversalFilterTable($this->header, null);

            $env = new Environment();
            $env->setTable($this->totaltable);

            // get executer for expression
            $expr = $this->filter->getExpression();
            $this->exprexec = $this->interpreter->findExecuterFor($expr);
            $this->exprexec->initExpression($expr, $env, $this->interpreter, true);
        }
    }
    
    public function getExpressionHeader() {
        return $this->header;
    }
    
    public function evaluateAsExpression() {
        $headerA =$this->executerA->getExpressionHeader();
        $sourcecontentA=$this->executerA->evaluateAsExpression();
        $acount = $sourcecontentA->getRowCount();
        $headerB =$this->executerA->getExpressionHeader();
        $sourcecontentB=$this->executerA->evaluateAsExpression();
        $bcount = $sourcecontentB->getRowCount();
        
        
        // niet zo efficiënt... bereken eerst volledig cartesiaans product... Maar beter dan rij per rij...
        $totalcontent = new UniversalFilterTableContent();
        $size = $sourcecontentA->getRowCount()*$sourcecontentB->getRowCount();
        for($a=0;$a<$acount;$a++){
            for($b=0;$b<$bcount;$b++){
                $row = new UniversalFilterTableContentRow();
                
                $rowA = $sourcecontentA->getRow($a);
                $rowB = $sourcecontentA->getRow($b);
                
                $globalindex = 0;
                for ($i=0;$i < $headerA->getColumnCount(); $i++){
                    $idA = $headerA->getColumnIdByIndex($i);
                    $value = $rowA->getCellValue($idA, true);
                    $row->defineValue($this->header->getColumnIdByIndex($globalindex), $value);
                    $globalindex++;
                }
                for ($i=0;$i < $headerB->getColumnCount(); $i++){
                    $idB = $headerB->getColumnIdByIndex($i);
                    $value = $rowB->getCellValue($idB, true);
                    $row->defineValue($this->header->getColumnIdByIndex($globalindex), $value);
                    $globalindex++;
                }
                
                $totalcontent->addRow($row);
            }
        }
        
        
        
        //filtered rows
        $filteredRows=null;
        
        
        
        if($this->filter->getExpression()!==NULL) {
            // table is ready
            $this->totaltable->setContent($totalcontent);
            
            //get expression header
            $exprheader = $this->exprexec->getExpressionHeader();

            // filter the content
            $filteredRows = new UniversalFilterTableContent();

            // calcultate the table with true and false
            $inResultTable = $this->exprexec->evaluateAsExpression();

            //loop all rows
            for ($index = 0; $index < $totalcontent->getRowCount(); $index++) {
                $row = $totalcontent->getRow($index);

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
        }else{
            $filteredRows = $totalcontent;
        }
        
        // TODO: here
        // keepLeft en keepRight !!!!!
        
        if($this->filter->getKeepRight() && $this->filter->getKeepLeft()){
            throw new Exception("TODO: FULL OUTER JOIN not supported yet...");
        }
        if($this->filter->getKeepLeft()){
            throw new Exception("TODO: LEFT OUTER JOIN not supported yet...");
        }
        if($this->filter->getKeepRight()){
            throw new Exception("TODO: RIGHT OUTER JOIN not supported yet...");
        }
        
        // end TODO
        
        
        
        $sourcecontentA->tryDestroyTable();
        $sourcecontentB->tryDestroyTable();
        
        return $filteredRows;
    }
    
    public function cleanUp(){
        $this->executerA->cleanUp();
        $this->executerB->cleanUp();
        if($this->exprexec!==NULL) {
            $this->exprexec->cleanUp();
        }
    }
    
    public function modififyFiltersWithHeaderInformation(){
        parent::modififyFiltersWithHeaderInformation();
        $this->executerA->modififyFiltersWithHeaderInformation();
        $this->executerB->modififyFiltersWithHeaderInformation();
        if($this->exprexec!==NULL) {
            $this->exprexec->modififyFiltersWithHeaderInformation();
        }
    }
    
    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex){
        $arr=array_merge(
            $this->executerA->filterSingleSourceUsages($this->filter, 0),
            $this->executerB->filterSingleSourceUsages($this->filter, 0),
            ($this->exprexec!==null?$this->exprexec->filterSingleSourceUsages($this->filter, -1):array()));// todo give correct number
        
        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }
}

?>

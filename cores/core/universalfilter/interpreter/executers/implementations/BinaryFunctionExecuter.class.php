<?php
/**
 * This file contains the abstact top class for all evaluators for binary functions
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
abstract class BinaryFunctionExecuter extends AbstractUniversalFilterNodeExecuter {
    
    private $header;
    
    private $executer1;
    private $executer2;
    
    private $header1;
    private $header2;
    
    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn){
        $this->filter = $filter;
        
        $this->executer1 = $interpreter->findExecuterFor($this->filter->getSource(0));
        $this->executer2 = $interpreter->findExecuterFor($this->filter->getSource(1));
        
        //init down
        $this->executer1->initExpression($this->filter->getSource(0), $topenv, $interpreter, true);
        $this->executer2->initExpression($this->filter->getSource(1), $topenv, $interpreter, true);
        
        $this->header1 = $this->executer1->getExpressionHeader();
        $this->header2 = $this->executer2->getExpressionHeader();
        
        //combined name
        $combinedName = $this->getName(
                $this->header1->getColumnNameById($this->header1->getColumnId()), 
                $this->header2->getColumnNameById($this->header2->getColumnId()));
        
        //column
        $cominedHeaderColumn = new UniversalFilterTableHeaderColumnInfo(array($combinedName));
        
        //single row?
        $isSingleRowByConstruction = $this->header1->isSingleRowByConstruction() && $this->header2->isSingleRowByConstruction();
        
        //new Header
        $this->header = new UniversalFilterTableHeader(array($cominedHeaderColumn), $isSingleRowByConstruction, true);
    }
    
    public function getExpressionHeader(){
        return $this->header;
    }
    
    public function evaluateAsExpression() {
        $table1content = $this->executer1->evaluateAsExpression();
        $table2content = $this->executer2->evaluateAsExpression();
        
        $idA = $this->header1->getColumnId();
        $idB = $this->header2->getColumnId();
        $finalid = $this->header->getColumnId();
        
        if(
                !$this->header1->isSingleRowByConstruction() && 
                !$this->header2->isSingleRowByConstruction() &&
                $table1content->getRowCount()!=$table2content->getRowCount()){
            throw new Exception("Columns differ in size");//Can that happen??????????
        }
        
        $rows=new UniversalFilterTableContent();
        
        $size=max(array($table1content->getRowCount(), $table2content->getRowCount()));
        
        //loop through all rows and evaluate the expression
        for ($i=0;$i<$size;$i++){
            $row=new UniversalFilterTableContentRow();
            
            //get the value for index i for both tables
            $valueA=null;
            $valueB=null;
            if($table1content->getRowCount()>$i){
                $valueA=$table1content->getValue($idA, $i, true);
            }else{
                $valueA=$table1content->getCellValue($idA, true);
            }
            if($table2content->getRowCount()>$i){
                $valueB=$table2content->getValue($idB, $i, true);
            }else{
                $valueB=$table2content->getCellValue($idB, true);
            }
            
            //evaluate
            $value = $this->doBinaryFunction($valueA, $valueB);
            
            $row->defineValue($finalid, $value);
            
            $rows->addRow($row);
        }
        
        $table1content->tryDestroyTable();
        $table2content->tryDestroyTable();
        
        //return the result
        return $rows;
    }
    
    
    
    public function getName($nameA, $nameB){
        return $nameA." combined ".$nameA;
    }
    
    public function doBinaryFunction($valueA, $valueB){
        return null;
    }
    
    public function cleanUp(){
        $this->executer1->cleanUp();
        $this->executer2->cleanUp();
    }
    
    public function modififyFiltersWithHeaderInformation(){
        parent::modififyFiltersWithHeaderInformation();
        $this->executer1->modififyFiltersWithHeaderInformation();
        $this->executer2->modififyFiltersWithHeaderInformation();
    }
    
    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex){
        $arr=array_merge(
            $this->executer1->filterSingleSourceUsages($this->filter, 0),
            $this->executer2->filterSingleSourceUsages($this->filter, 1));
        
        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }
}
?>

<?php

/**
 * Executes the ColumnSelectionFilter filter
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

include_once 'universalfilter/common/StableSorting.php';

/* sorting methods... */
function SortFieldsFilterCompare($obj1, $obj2, $dir=-1) {
    $str1 = $obj1["data"];
    $str2 = $obj2["data"];
    
    if($str1==$str2){
        return 0;
    }else if($str1<$str2){
        return $dir*-1;
    }else{
        return $dir*1;
    }
}

function SortFieldsFilterCompareAsc($obj1, $obj2){
    return SortFieldsFilterCompare($obj1, $obj2, 1);
}

function SortFieldsFilterCompareDesc($obj1, $obj2){
    return SortFieldsFilterCompare($obj1, $obj2, -1);
}

/* the executer */
class SortFieldsFilterExecuter extends AbstractUniversalFilterNodeExecuter {
    
    private $header;
    
    private $executer;
    
    private $interpreter;
    
    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn) {
        $this->filter = $filter;
        $this->interpreter = $interpreter;
        
        //get source environment header
        $executer = $interpreter->findExecuterFor($filter->getSource());
        $this->executer = $executer;
        
        $executer->initExpression($filter->getSource(), $topenv, $interpreter, $preferColumn);
        $this->header = $executer->getExpressionHeader();
    }
    
    public function getExpressionHeader() {
        return $this->header;
    }
    
    private function toArray($columnId, $content) {
        $newarr = array();
        for ($i = 0; $i < $content->getRowCount(); $i++) {
            $row = $content->getRow($i);
            $data = $row->getCellValue($columnId,false);
            $newarr[$i] = array("origindex" => $i, "data" => $data);
        }
        return $newarr;
    }
    
    public function evaluateAsExpression() {
        $sourcecontent=$this->executer->evaluateAsExpression();
        
        $sortedcontent=$sourcecontent;
        
        
        $columns = $this->filter->getColumnData();
        $columnsrev = array_reverse($columns);//apply in inverse order
        
        foreach($columnsrev as $column){
            //this column
            $filterColumn =$column->getColumn();
            //find something to evaluate it
            $exprexec = $this->interpreter->findExecuterFor($filterColumn);
            
            //environment
            $env = new Environment();
            $env->setTable(new UniversalFilterTable($this->header, $sortedcontent));
            
            //init expression
            $exprexec->initExpression($filterColumn, $env, $this->interpreter, true);
            $columnheader = $exprexec->getExpressionHeader();
            
            //check
            if(!$columnheader->isSingleColumnByConstruction()){
                throw new Exception("Can not sort on '*' ");
            }
            
            //get the content of the column
            $columncontent = $exprexec->evaluateAsExpression();
            
            //convert to array
            $arr = $this->toArray($columnheader->getColumnId(),$columncontent);
            
            //order
            $order = ($column->getSortOrder()==SortFieldsFilterColumn::$SORTORDER_ASCENDING?"SortFieldsFilterCompareAsc":"SortFieldsFilterCompareDesc");
            
            //do assiocative sort
            //asort($arr);   // --- !!!!!! The version in php is NOT stable!
            mergesort($arr, $order);
            
            //create new content
            $newsortedcontent = new UniversalFilterTableContent();
            
            foreach ($arr as $key => $value) {
                $index=$value["origindex"];
                $newsortedcontent->addRow($sortedcontent->getRow($index));
            }
            $sortedcontent->tryDestroyTable();
            $sortedcontent=$newsortedcontent;
            
            //cleanup
            $columncontent->tryDestroyTable();
            $exprexec->cleanUp();
        }
        
        return $sortedcontent;
    }
    
    public function cleanUp(){
        $this->executer->cleanUp();
    }
    
    public function modififyFiltersWithHeaderInformation(){
        parent::modififyFiltersWithHeaderInformation();
        $this->executer->modififyFiltersWithHeaderInformation();
    }
    
    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex){
        $arr=$this->executer->filterSingleSourceUsages($this->filter, 0);
        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }
    
}

?>

<?php

/**
 * Executes the LimitFilter filter
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */
class LimitFilterExecuter extends AbstractUniversalFilterNodeExecuter {
    
    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn) {
        $this->filter = $filter;
        
        //get source environment header
        $executer = $interpreter->findExecuterFor($filter->getSource());
        $this->executer = $executer;

        $this->executer->initExpression($filter->getSource(),$topenv,$interpreter,$preferColumn);
        
        $this->header = $this->executer->getExpressionHeader();
        
    }
    
    public function getExpressionHeader() {
        return $this->header;
    }
    
    public function evaluateAsExpression() {

        $sourceheader =$this->executer->getExpressionHeader();
        $sourcecontent=$this->executer->evaluateAsExpression();
        
        //create a new empty output table

        $newRows = new UniversalFilterTableContent();

        $offset = $this->filter->getOffset();
        $limit = $this->filter->getLimit();

        for($index = $offset; $index < $offset + $limit; $index++){
            try{
                $newRows->addRow($sourcecontent->getRow($index));
            }catch(Exception $ex){
                // this exception will occur when we go out of bounds in our source
                // do nothing, the row just doesn't get added, and ofcourse subsequent rows won't be added as well 
                // for they do not exist.
                break;
            }
        }
        
        $sourcecontent->tryDestroyTable();
        
        return $newRows;
    }
    
    public function cleanUp(){
        $this->executer->cleanUp();
    }
 
    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex){
        $arr=$this->executer->filterSingleSourceUsages($this->filter, 0); 
       
        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }
    
}

?>

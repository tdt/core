<?php

/**
 * "Executes" a constant and returns a table
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace  tdt\core\universalfilter\interpreter\executers\implementations;

class ConstantExecuter extends tdt\core\universalfilter\interpreter\executers\base\AbstractUniversalFilterNodeExecuter {
    
    private $header;
    
    private $const;
    private $nameOfField;
    
    private function getFieldName($const){
        if($const!=""){
            return "$const";
        }else{
            return "empty";
        }
    }
    
    public function initExpression(tdt\core\universalfilter\UniversalFilterNode $filter, tdt\core\universalfilter\interpreter\Environment $topenv, tdt\core\universalfilter\interpreter\IInterpreterControl $interpreter, $preferColumn){
        $this->filter = $filter;
        
        $this->const=$filter->getConstant();
        $this->nameOfField=$this->getFieldName($this->const);
        
        //column
        $cominedHeaderColumn = new tdt\core\universalfilter\data\UniversalFilterTableHeaderColumnInfo(array($this->nameOfField));
        
        //new Header
        $this->header = new tdt\core\universalfilter\data\UniversalFilterTableHeader(array($cominedHeaderColumn), true, true);
    }
    
    public function getExpressionHeader(){
        return $this->header;
    }
    
    public function evaluateAsExpression() {
        $id = $this->header->getColumnId();
        
        $row=new tdt\core\universalfilter\data\UniversalFilterTableContentRow();
        $row->defineValue($id, $this->const);
        
        $content = new tdt\core\universalfilter\data\UniversalFilterTableContent();
        $content->addRow($row);
        
        return $content;
    }
    
    public function filterSingleSourceUsages(tdt\core\universalfilter\UniversalFilterNode $parentNode, $parentIndex) {
        return array();
    }
}

?>

<?php

/**
 * Print the tree...
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class TreePrinter {
    
    private $depth=0;
    
    /**
     * Converts a UniversalFilterNode to a string you can print...
     * @param UniversalFilterNode $tree
     * @return string A string representation of the tree
     */
    public function treeToString(UniversalFilterNode $tree=null){
        if($tree==null){
            return $this->getPadding()."[!!!! NULL !!!!]";
        }
        
        $method = "print_".get_class($tree);
        //calls the correct clone method and then returns.
        $var = "";
        if(method_exists($this, $method)){
            $var = $this->$method($tree);
        }else{
            $var = $this->printSomeUnknownNode($tree);
        }
        return $var;
    }
    
    public function treeToStringWithPadding($padding, UniversalFilterNode $tree=null){
        $this->incPadding($padding);
        $string=$this->treeToString($tree);
        $this->incPadding(-$padding);
        return $string;
    }
    
    public function printString(UniversalFilterNode $tree=null){
        $string = $this->treeToString($tree);
        echo "<div style='border:1px solid grey'>";
        echo "<pre>";
        echo "$string";
        echo "</pre>";
        echo "</div>";
    }
    
    private function getPadding($dir=0){
        $padding="";
        for ($index = 0; $index < $this->depth+$dir; $index++) {
            $padding.="  |";
        }
        return $padding;
    }
    
    private function incPadding($count){
        $this->depth+=$count;
        return "";
    }
    
    private function print_Identifier(Identifier $filter){
        return  $this->getPadding()."Identifier[ ".$filter->getIdentifierString()." ]\n";
    }
    
    private function print_Constant(Constant $filter){
        return  $this->getPadding()."Constant[ ".$filter->getConstant()." ]\n";
    }
    
    private function print_TableAliasFilter(TableAliasFilter $filter){
        return  $this->getPadding()."TableAliasFilter[".$filter->getAlias()."] {\n".
                $this->getPadding(1)."source: \n".
                $this->treeToStringWithPadding(2, $filter->getSource()).
                $this->getPadding()."}\n";
    }
    
    private function print_FilterByExpressionFilter(FilterByExpressionFilter $filter){
        return  $this->getPadding()."FilterByExpressionFilter {\n".
                $this->getPadding(1)."expression : \n".
                $this->treeToStringWithPadding(2, $filter->getExpression()).
                $this->getPadding(1)."source: \n".
                $this->treeToStringWithPadding(2, $filter->getSource()).
                $this->getPadding()."}\n";
    }
    
    private function print_ColumnSelectionFilter(ColumnSelectionFilter $filter){
        $string = $this->getPadding()."ColumnSelectionFilter {\n";
        foreach ($filter->getColumnData() as $index => $originalColumn) {
            $aliaspart="";
            if($originalColumn->getAlias()!=null){
                 $aliaspart=" [As ".$originalColumn->getAlias()."]";
            }
            $string.=$this->getPadding(1)."column ".($index+1).$aliaspart.": \n";
            $string.=$this->treeToStringWithPadding(2, $originalColumn->getColumn());
        }
        
        $string.=$this->getPadding(1)."source: \n".
                $this->treeToStringWithPadding(2, $filter->getSource()).
                $this->getPadding()."}\n";
        
        return $string;
    }
    
    private function print_SortFieldsFilter(SortFieldsFilter $filter){
        $string = $this->getPadding()."SortFieldsFilter {\n";
        foreach ($filter->getColumnData() as $index => $originalColumn) {
            $name = $originalColumn->getColumn()->getIdentifierString();   
            $order = ($originalColumn->getSortOrder()==SortFieldsFilterColumn::$SORTORDER_ASCENDING?"ascending":"descending");
            $string.=$this->getPadding(1)."sort column ".$name." ".$order." \n";
        }
        
        $string.=$this->getPadding(1)."in source: \n".
                $this->treeToStringWithPadding(2, $filter->getSource()).
                $this->getPadding()."}\n";
        
        return $string;
    }
    
    private function print_DistinctFilter(DistinctFilter $filter){
        return  $this->getPadding()."DistinctFilter {\n".
                $this->getPadding(1)."source: \n".
                $this->treeToStringWithPadding(2, $filter->getSource()).
                $this->getPadding()."}\n";
    }

    private function print_LimitFilter(LimitFilter $filter){
        return  $this->getPadding()."LimitFilter {\n".
                $this->getPadding(1)."source: \n".
                $this->treeToStringWithPadding(2, $filter->getSource()).
                $this->getPadding()."}\n";
    }
    
    private function print_DataGrouper(DataGrouper $filter){
        $columnstring="";
        foreach ($filter->getColumns() as $index => $column){
            if($index!=0){$columnstring.=", ";}
            $columnstring.=$column->getIdentifierString();
        }
        
        return  $this->getPadding()."DataGrouper[".$columnstring."] {\n".
                $this->getPadding(1)."source: \n".
                $this->treeToStringWithPadding(2, $filter->getSource()).
                $this->getPadding()."}\n";
    }
    
    private function print_DatasetJoinFilter(DatasetJoinFilter $filter){
        $string = $this->getPadding()."DatasetJoinFilter [keepleft: ".($filter->getKeepLeft()?"true":"false")."; keepright: ".($filter->getKeepRight()?"true":"false")."] {\n".
        $this->getPadding(1)."source 1: \n".
        $this->treeToStringWithPadding(2, $filter->getSource(0)).
        $this->getPadding(1)."source 2: \n".
        $this->treeToStringWithPadding(2, $filter->getSource(1));
        if($filter->getExpression()!==NULL) {
            $string .= $this->getPadding(1)."expression : \n".
            $this->treeToStringWithPadding(2, $filter->getExpression());
        }else{
            $string .= $this->getPadding(1)."expression : &lt;NONE&gt; \n";
        }
        $string .= $this->getPadding()."}\n";
        return $string;
    }
    
    private function print_UnaryFunction(UnaryFunction $filter){
        return  $this->getPadding()."UnaryFunction[".$filter->getType()."] {\n".
                $this->getPadding(1)."argument: \n".
                $this->treeToStringWithPadding(2, $filter->getSource()).
                $this->getPadding()."}\n";
    }
    
    private function print_BinaryFunction(BinaryFunction $filter){
        return  $this->getPadding()."BinaryFunction[".$filter->getType()."] {\n".
                $this->getPadding(1)."argument 1: \n".
                $this->treeToStringWithPadding(2, $filter->getSource(0)).
                $this->getPadding(1)."argument 2: \n".
                $this->treeToStringWithPadding(2, $filter->getSource(1)).
                $this->getPadding()."}\n";
    }
    
    private function print_TernaryFunction(TernaryFunction $filter){
        return  $this->getPadding()."TernaryFunction[".$filter->getType()."] {\n".
                $this->getPadding(1)."argument 1: \n".
                $this->treeToStringWithPadding(2, $filter->getSource(0)).
                $this->getPadding(1)."argument 2: \n".
                $this->treeToStringWithPadding(2, $filter->getSource(1)).
                $this->getPadding(1)."argument 3: \n".
                $this->treeToStringWithPadding(2, $filter->getSource(2)).
                $this->getPadding()."}\n";
    }
    
    private function print_AggregatorFunction(AggregatorFunction $filter){
        return  $this->getPadding()."AggregatorFunction[".$filter->getType()."] {\n".
                $this->getPadding(1)."argument: \n".
                $this->treeToStringWithPadding(2, $filter->getSource()).
                $this->getPadding()."}\n";
    }
    
    private function print_CheckInFunction(CheckInFunction $filter){
        $checkstring="";
        foreach ($filter->getConstants() as $index => $constant){
            if($index!=0){$checkstring.=", ";}
            $checkstring.="\"".$constant->getConstant()."\"";
        }
        
        return  $this->getPadding()."CheckInFunction[".$checkstring."] {\n".
                $this->getPadding(1)."source: \n".
                $this->treeToStringWithPadding(2, $filter->getSource()).
                $this->getPadding()."}\n";
    }
    
    private function printSomeUnknownNode(UniversalFilterNode $filter){
        $string = $this->getPadding().$filter->getType()."[?]";
        if($filter instanceof NormalFilterNode){
            $string.=" {\n";
            for ($index = 0; $index < $filter->getSourceCount(); $index++) {
                $string.=$this->getPadding(1)."source ".($index+1).": \n";
                $string.=$this->treeToStringWithPadding(2, $filter->getSource($index));
            }
            $string.=$this->getPadding()."}\n";
            return $string;
        }else{
            return $string."\n";
        }
    }
    
}
?>

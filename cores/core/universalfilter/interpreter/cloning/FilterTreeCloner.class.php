<?php

/**
 * This file contains methods to make a deep copy of the given UniversalFilterTree
 * 
 * @package The-Datatank/universalfilter/interpreter/cloning
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class FilterTreeCloner {
    /**
     * Main method... (And only public method)
     * @param UniversalFilterNode $tree
     * @return UniversalFilterNode a deep copy of the tree
     */
    public function deepCopyTree(UniversalFilterNode $tree){
        $method = "clone_".get_class($tree);
        //calls the correct clone method and then returns.
        return $this->$method($tree);
    }
    
    private function clone_Identifier(Identifier $filter){
        return new Identifier($filter->getIdentifierString());
    }
    
    private function clone_Constant(Constant $filter){
        return new Constant($filter->getConstant());
    }
    
    private function clone_TableAliasFilter(TableAliasFilter $filter){
        return new TableAliasFilter($filter->getAlias(), $this->deepCopyTree($filter->getSource()));
    }
    
    private function clone_FilterByExpressionFilter(FilterByExpressionFilter $filter){
        return new FilterByExpressionFilter($this->deepCopyTree($filter->getExpression()), $this->deepCopyTree($filter->getSource()));
    }
    
    private function clone_ColumnSelectionFilter(ColumnSelectionFilter $filter){
        $newColumnData = array();
        foreach ($filter->getColumnData() as $originalColumn) {
            array_push($newColumnData, new ColumnSelectionFilterColumn($this->deepCopyTree($originalColumn->getColumn()), $originalColumn->getAlias()));
        }
        
        return new ColumnSelectionFilter($newColumnData, $this->deepCopyTree($filter->getSource()));
    }
    
    private function clone_SortFieldsFilter(SortFieldsFilter $filter){
        $newColumnData = array();
        foreach ($filter->getColumnData() as $originalColumn) {
            array_push($newColumnData, new SortFieldsFilterColumn($this->deepCopyTree($originalColumn->getColumn()), $originalColumn->getSortOrder()));
        }
        
        return new SortFieldsFilter($newColumnData, $this->deepCopyTree($filter->getSource()));
    }
    
    private function clone_DistinctFilter(DistinctFilter $filter){
        return new DistinctFilter($this->deepCopyTree($filter->getSource()));
    }

    private function clone_LimitFilter(LimitFilter $filter){
        return new LimitFilter($this->deepCopyTree($filter->getSource()),$filter->getOffset(),$filter->getLimit());
    }
    
    private function clone_DataGrouper(DataGrouper $filter){
        return new DataGrouper($filter->getColumns(), $this->deepCopyTree($filter->getSource()));
    }

    private function clone_DatasetJoinFilter(DatasetJoinFilter $filter){
        $expr=$filter->getExpression();
        if($expr!==NULL) {
            $expr = $this->deepCopyTree($expr);
        }
        return new DatasetJoinFilter($filter->getKeepLeft(), $filter->getKeepRight(), $this->deepCopyTree($filter->getSource(0)), $this->deepCopyTree($filter->getSource(1)));
    }
    
    private function clone_UnaryFunction(UnaryFunction $filter){
        return new UnaryFunction($filter->getType(), 
                $this->deepCopyTree($filter->getSource(0)));
    }
    
    private function clone_BinaryFunction(BinaryFunction $filter){
        return new BinaryFunction($filter->getType(), 
                $this->deepCopyTree($filter->getSource(0)), 
                $this->deepCopyTree($filter->getSource(1)));
    }
    
    private function clone_TernaryFunction(TernaryFunction $filter){
        return new TernaryFunction($filter->getType(), 
                $this->deepCopyTree($filter->getSource(0)), 
                $this->deepCopyTree($filter->getSource(1)), 
                $this->deepCopyTree($filter->getSource(2)));
    }
    
    private function clone_AggregatorFunction(AggregatorFunction $filter){
        return new AggregatorFunction($filter->getType(), $this->deepCopyTree($filter->getSource()));
    }
    
    private function clone_CheckInFunction(CheckInFunction $filter){
        $checkInConstants = array();
        foreach ($filter->getConstants() as $originalConstant) {
            array_push($checkInConstants, $this->deepCopyTree($originalConstant));
        }
        
        return new CheckInFunction($checkInConstants, $this->deepCopyTree($filter->getSource()));
    }
}

?>

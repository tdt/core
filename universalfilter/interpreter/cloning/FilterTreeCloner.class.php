<?php

/**
 * This file contains methods to make a deep copy of the given UniversalFilterTree
 * 
 * @package The-Datatank/universalfilter/interpreter/cloning
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\interpreter\cloning;

class FilterTreeCloner {
    /**
     * Main method... (And only public method)
     * @param UniversalFilterNode $tree
     * @return UniversalFilterNode a deep copy of the tree
     */
    public function deepCopyTree(tdt\core\universalfilter\UniversalFilterNode $tree){
        $method = "clone_".get_class($tree);
        //calls the correct clone method and then returns.
        return $this->$method($tree);
    }
    
    private function clone_Identifier(tdt\core\universalfilter\Identifier $filter){
        return new tdt\core\universalfilter\Identifier($filter->getIdentifierString());
    }
    
    private function clone_Constant(tdt\core\universalfilter\Constant $filter){
        return new tdt\core\universalfilter\Constant($filter->getConstant());
    }
    
    private function clone_TableAliasFilter(tdt\core\universalfilter\TableAliasFilter $filter){
        return new tdt\core\universalfilter\TableAliasFilter($filter->getAlias(), $this->deepCopyTree($filter->getSource()));
    }
    
    private function clone_FilterByExpressionFilter(tdt\core\universalfilter\FilterByExpressionFilter $filter){
        return new tdt\core\universalfilter\FilterByExpressionFilter($this->deepCopyTree($filter->getExpression()), $this->deepCopyTree($filter->getSource()));
    }
    
    private function clone_ColumnSelectionFilter(tdt\core\universalfilter\ColumnSelectionFilter $filter){
        $newColumnData = array();
        foreach ($filter->getColumnData() as $originalColumn) {
            array_push($newColumnData, new tdt\core\universalfilter\ColumnSelectionFilterColumn($this->deepCopyTree($originalColumn->getColumn()), $originalColumn->getAlias()));
        }
        
        return new tdt\core\universalfilter\ColumnSelectionFilter($newColumnData, $this->deepCopyTree($filter->getSource()));
    }
    
    private function clone_SortFieldsFilter(tdt\core\universalfilter\SortFieldsFilter $filter){
        $newColumnData = array();
        foreach ($filter->getColumnData() as $originalColumn) {
            array_push($newColumnData, new tdt\core\universalfilter\SortFieldsFilterColumn($this->deepCopyTree($originalColumn->getColumn()), $originalColumn->getSortOrder()));
        }
        
        return new tdt\core\universalfilter\SortFieldsFilter($newColumnData, $this->deepCopyTree($filter->getSource()));
    }
    
    private function clone_DistinctFilter(tdt\core\universalfilter\DistinctFilter $filter){
        return new tdt\core\universalfilter\DistinctFilter($this->deepCopyTree($filter->getSource()));
    }

    private function clone_LimitFilter(tdt\core\universalfilter\LimitFilter $filter){
        return new tdt\core\universalfilter\LimitFilter($this->deepCopyTree($filter->getSource()),$filter->getOffset(),$filter->getLimit());
    }
    
    private function clone_DataGrouper(tdt\core\universalfilter\DataGrouper $filter){
        return new tdt\core\universalfilter\DataGrouper($filter->getColumns(), $this->deepCopyTree($filter->getSource()));
    }

    private function clone_DatasetJoinFilter(tdt\core\universalfilter\DatasetJoinFilter $filter){
        $expr=$filter->getExpression();
        if($expr!==NULL) {
            $expr = $this->deepCopyTree($expr);
        }
        return new tdt\core\universalfilter\DatasetJoinFilter($filter->getKeepLeft(), $filter->getKeepRight(), $this->deepCopyTree($filter->getSource(0)), $this->deepCopyTree($filter->getSource(1)));
    }
    
    private function clone_UnaryFunction(tdt\core\universalfilter\UnaryFunction $filter){
        return new tdt\core\universalfilter\UnaryFunction($filter->getType(), 
                $this->deepCopyTree($filter->getSource(0)));
    }
    
    private function clone_BinaryFunction(tdt\core\universalfilter\BinaryFunction $filter){
        return new tdt\core\universalfilter\BinaryFunction($filter->getType(), 
                $this->deepCopyTree($filter->getSource(0)), 
                $this->deepCopyTree($filter->getSource(1)));
    }
    
    private function clone_TernaryFunction(tdt\core\universalfilter\TernaryFunction $filter){
        return new tdt\core\universalfilter\TernaryFunction($filter->getType(), 
                $this->deepCopyTree($filter->getSource(0)), 
                $this->deepCopyTree($filter->getSource(1)), 
                $this->deepCopyTree($filter->getSource(2)));
    }
    
    private function clone_AggregatorFunction(tdt\core\universalfilter\AggregatorFunction $filter){
        return new tdt\core\universalfilter\AggregatorFunction($filter->getType(), $this->deepCopyTree($filter->getSource()));
    }
    
    private function clone_CheckInFunction(tdt\core\universalfilter\CheckInFunction $filter){
        $checkInConstants = array();
        foreach ($filter->getConstants() as $originalConstant) {
            array_push($checkInConstants, $this->deepCopyTree($originalConstant));
        }
        
        return new tdt\core\universalfilter\CheckInFunction($checkInConstants, $this->deepCopyTree($filter->getSource()));
    }
}

?>

<?php

/*
 * This class converts a query built in the AST into an Elastic search query at best effort.
 */

class ElasticSearchConverter{
    
    private $query;
    // the SELECT identifiers
    private $identifiers = array();
    private $IN_SELECT_CLAUSE = TRUE;
    private $selectClausePresent  = FALSE;
    private $headerNames;
    private $groupby = "";
    private $orderby ="";
 
    
    public function getOrderBy(){
        
    }

    public function __construct($headerNames) {
        $this->headerNames = $headerNames;
        $this->query = new stdClass();
    }

    // this function will be deleted if the TODO functionality is implemented
    public function getGroupBy() {
       
    }

    /**
     * Converts a UniversalFilterNode to a string you can print...
     * @param UniversalFilterNode $tree
     * @return string A string representation of the tree
     */
    public function treeToSQL(UniversalFilterNode $tree) {
        $method = "print_" . get_class($tree);
        //calls the correct clone method and then returns.
        $this->$method($tree);
        
        /*
         * Sometimes only an identifier can be present, without any clauses
         * So we check for the "selectclausepresent" if we have a select clause
         * If not, we add one. If we have this situation we also know that the identifier
         * is the resource identifier, for there are no columnselectionfilter identifiers, or 
         * filter identifiers ( they need a select clause )
         */
        if(!$this->selectClausePresent){
                  
        }
      return $this->query;
    }

    private function print_Identifier(Identifier $filter) {
       
    }

    public function getIdentifiers() {
        return $this->identifiers;
    }

    private function print_Constant(Constant $filter) {
        // just add it to the string       
    }

    private function print_TableAliasFilter(TableAliasFilter $filter) {
        // not implemented yet
    }

    private function print_SortFieldsFilter(SortFieldsFilter $filter) {
      
    }

    private function print_FilterByExpressionFilter(FilterByExpressionFilter $filter) {
       $this->treeToSQL($filter->getExpression());
    }

    private function print_ColumnSelectionFilter(ColumnSelectionFilter $filter) {
                    
    }

    private function print_DistinctFilter(DistinctFilter $filter) {
        // not supported yet
    }

    private function print_DataGrouper(DataGrouper $filter) {        
     
    }

    private function print_UnaryFunction(UnaryFunction $filter) {
       
        switch ($filter->getType()) {
            case UnaryFunction::$FUNCTION_UNARY_UPPERCASE:
              
                break;
            case UnaryFunction::$FUNCTION_UNARY_LOWERCASE:
              
                break;
            case UnaryFunction::$FUNCTION_UNARY_STRINGLENGTH:
               
                break;
            case UnaryFunction::$FUNCTION_UNARY_ROUND:
               break;
            case UnaryFunction::$FUNCTION_UNARY_ISNULL:
                
                break;
            case Un:
                
                break;
            default:
                break;
        }
    }

    private function print_BinaryFunction(BinaryFunction $filter) {
      

        switch ($filter->getType()) {
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_EQUAL:
                
                break;
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_THAN:
                
                break;
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_THAN:
                
                break;
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN:
                
                break;
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN:
                
                break;
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_NOTEQUAL:
                
                break;
            case BinaryFunction::$FUNCTION_BINARY_AND:
                
                break;            
            default:
                break;
        }
    }

    private function print_TernaryFunction(TernaryFunction $filter) {
        // not supported yet
    }

    private function print_AggregatorFunction(AggregatorFunction $filter) {
        //TODO the rest of the aggregators
       
        switch ($filter->getType()) {
            case AggregatorFunction::$AGGREGATOR_COUNT:
                        
                break;           
            default:
                break;
        }
    }

    private function print_CheckInFunction(CheckInFunction $filter) {
        // not supported yet
    }

}
?>

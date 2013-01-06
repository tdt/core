<?php
/**
 * This file contains an uniform representation of a query tree
 *
 * @package The-Datatank/universalfilter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

/**
 * Top class of all filters
 */
abstract class UniversalFilterNode {
    private $type;
    private $attachments;
    
    public function __construct($type) {
        $this->type=$type;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function attach($id, $data) {
        $this->attachments[$id]=$data;
    }
    
    public function getAttachment($id) {
        return $this->attachments[$id];
    }
}

/**
 * Represents an identifier... 
 *
 * ... of a Table, a Column or an Alias of one of the two.
 *
 * formats: (interpreted by the UniversalTableManager...)
 *   A) package.package.resource.restparam.restparam:subresource.subresource
 *       with: 
 *          * packages: one or more, 
 *          * restparams: optional,
 *          * subresources: optional
 *       (Please note the separators!)
 * 
 *   B) alias.name_of_column
 * 
 * 
 */
class Identifier extends UniversalFilterNode {
    private $value;//type:String
    
    public function __construct($value) {
        parent::__construct("IDENTIFIER");
        $this->value=$value;
    }
    
    public function getIdentifierString() {
        return $this->value;
    }
}

/**
 * Represents a constant
 * Can be a string, a boolean, or a number.
 */
class Constant extends UniversalFilterNode {
    private $constant;//type:String
    
    public function __construct($constant) {
        parent::__construct("CONSTANT");
        $this->constant=$constant;
    }
    
    public function getConstant() {
        return $this->constant;
    }
}

/**
 * *Top class* of all real filters
 * -> all these filters have one or more sources
 * 
 * Some filters like joins or binary functions have more than one source.
 */
abstract class NormalFilterNode extends UniversalFilterNode {
    private $source=array();//of UniversalFilterNode
    
    /**
     * Is this index a correct index of a source?
     * @param int $index 
     */
    private function checkBounds($index){
        if($index<0 || $index>=$this->getSourceCount()){
            throw new Exception("That is not a valid source-index for this kind of node (node kind: ".get_class($this).", index: ".$index.")");
        }
    }
    
    /**
     * Sets a source on this NormalFilterNode
     * 
     * @param UniversalFilterNode $source
     * @param int $index The index of the source to set. Default: source 0.
     */
    public function setSource(UniversalFilterNode $source, $index=0){
        $this->checkBounds($index);
        $this->source[$index]=$source;
    }
    
    /**
     * Gets a source of this NormalFilterNode
     * @param int $index
     * @return NormalFilterNode the sourcefilter
     */
    public function getSource($index=0){
        $this->checkBounds($index);
        if(isset($this->source[$index])){
            return $this->source[$index];
        }else{
            return null;
        }
    }
    
    /**
     * How many sources does this filter have? (Most of the time: 1)
     * @return int 
     */
    public function getSourceCount(){
        return 1;
    }
}

/**
 * Represents a table alias
 * Has a source and a alias string
 */
class TableAliasFilter extends NormalFilterNode {
    private $alias;//type:String
    
    public function __construct($alias, UniversalFilterNode $source=null) {
        parent::__construct("TABLEALIAS");
        $this->alias=$alias;
        if($source!=null) $this->setSource($source);
    }
    
    public function getAlias() {
        return $this->alias;
    }
}

/**
 * Represents a filter that keeps the row if expression results in true
 * expression is a filter too.
 *
 * type: Table -> Table
 * type: GroupedTable -> GroupedTable
 *
 * aka "WHERE" or "HAVING"
 */
class FilterByExpressionFilter extends NormalFilterNode{
    private $expression;//type:UniversalFilterNode
    
    public function __construct(UniversalFilterNode $expression, UniversalFilterNode $source=null) {
        parent::__construct("FILTEREXPRESSION");
        $this->expression=$expression;
        if($source!=null) $this->setSource($source);
    }
    
    public function getExpression(){
        return $this->expression;
    }
}

/**
 * Represents a filter that filters columns by applying the filters in $columndata 
 *
 * The resulting table is never grouped!
 * 
 * Table aliases will be removed when executing this filter
 * 
 * type: Table -> Table
 * type: GroupedTable -> Table
 *
 * aka "SELECT"
 */
class ColumnSelectionFilter extends NormalFilterNode {
    private $columndata;//type:Array[ColumnSelectionFilterColumn]

    public function __construct(array /* of ColumnSelectionFilterColumn */ $columndata, UniversalFilterNode $source=null) {
        parent::__construct("FILTERCOLUMN");
        $this->columndata=$columndata;
        if($source!=null) $this->setSource($source);
    }
    
    public function getColumnData(){
        return $this->columndata;
    }
}

/** Represents a column used in the ColumnSelectionFilter */
class ColumnSelectionFilterColumn {
    private $column;//type:UniversalFilterNode
    private $alias;//type:String (can be null)

    public function __construct(UniversalFilterNode $column, $alias=null) {
        $this->column=$column;
        $this->alias=$alias;
    }
    
    public function getColumn(){
        return $this->column;
    }
    
    public function getAlias(){
        return $this->alias;
    }
}


/**
 * Represents a filter that sorts the columns given in $columndata.
 * 
 * type: Table -> Table
 *
 * aka "ORDER BY"
 */
class SortFieldsFilter extends NormalFilterNode {
    private $columndata;//type:Array[SortFieldsFilterColumn]

    public function __construct(array /* of SortFieldsFilterColumn */ $columndata, UniversalFilterNode $source=null) {
        parent::__construct("FILTERSORTCOLUMNS");
        $this->columndata=$columndata;
        if($source!=null) $this->setSource($source);
    }
    
    public function getColumnData(){
        return $this->columndata;
    }
}

/** Represents a column used in the SortFieldsFilter */
class SortFieldsFilterColumn {
    private $column;//type:Identifier
    private $sortorder;//type:boolean
    
    public static $SORTORDER_ASCENDING = true;
    public static $SORTORDER_DESCENDING = false;

    public function __construct(Identifier $column, $sortorder=true) {
        $this->column=$column;
        $this->sortorder=$sortorder;
    }
    
    public function getColumn(){
        return $this->column;
    }
    
    public function getSortOrder(){
        return $this->sortorder;
    }
}


/**
 * Represents a distinct filter => keeps only the rows that are distinct
 *
 * type: Table -> Table
 *
 * aka "DISTINCT"
 */
class DistinctFilter extends NormalFilterNode{
    public function __construct(UniversalFilterNode $source=null) {
        parent::__construct("FILTERDISTINCT");
        if($source!=null) $this->setSource($source);
    }
}

/**
 * Represents a limit filter => keeps a certain amount of rows from a certain offset
 * Note that rows start counting from 0 thus limit(0,10) will return the first 10 rows.
 *
 * type: Table -> Table
 *
 * aka "LIMIT"
 */
class LimitFilter extends NormalFilterNode{
    public function __construct(UniversalFilterNode $source=null, $offset,$limit) {
        parent::__construct("FILTERLIMIT");
        if($source!=null) $this->setSource($source);
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function setOffset($offset){
        $this->offset = $offset;
    }
    
    public function getOffset(){
        return $this->offset;
    }
    
    public function setLimit($limit){
        $this->limit = $limit;
    }
    
    public function getLimit(){
        return $this->limit;
    }

}



/**
 * Groups the data (not really a filter)
 * When the data is grouped, it can not be grouped again.
 * Futhermore it can be filtered only by a select number of filters:
 *  - FilterByExpression
 *  - ColumnSelectionFilter (after this node the data is ungrouped again)
 *
 * type: Table -> GroupedTable
 *
 * aka "GROUP BY"
 */
class DataGrouper extends NormalFilterNode {
    private $columns;
    
    public function __construct(array $columns, UniversalFilterNode $source=null) {
        parent::__construct("DATAGROUPER");
        $this->columns=$columns;
        if($source!=null) $this->setSource($source);
    }
    
    public function getColumns(){
        return $this->columns;
    }
}

/**
 * Represents a filter that joins dataSets...
 *
 * type: Table -> Table
 *
 * aka "INNER JOIN"
 * aka "LEFT OUTER JOIN"
 * aka "RIGHT OUTER JOIN"
 * aka "FULL OUTER JOIN"
 * aka "CROSS JOIN"
 */
class DatasetJoinFilter extends NormalFilterNode{
    private $expression;//type:UniversalFilterNode
    private $keepleft;//type:boolean
    private $keepright;//type:boolean
    
    public function __construct($keepleft=false, $keepright=false, UniversalFilterNode $sourceA=null, UniversalFilterNode $sourceB=null, UniversalFilterNode $expression=null) {
        parent::__construct("JOIN");
        $this->expression=$expression;
        $this->keepleft=$keepleft;
        $this->keepright=$keepright;
        if($sourceA!=null) $this->setSource($sourceA, 0);
        if($sourceB!=null) $this->setSource($sourceB, 1);
    }
    
    public function getExpression(){
        return $this->expression;
    }
    
    public function getSourceCount(){
        return 2;
    }
    
    public function getKeepLeft(){
        return $this->keepleft;
    }
    
    public function getKeepRight(){
        return $this->keepright;
    }
}

/*
 * 
 *  --- FUNCTIONS --- 
 * 
 */

/**
 * This class represents all unary functions
 * 
 * type: Column -> Column
 * type: Cell -> Cell
 */
class UnaryFunction extends NormalFilterNode {
    
    public static $FUNCTION_UNARY_UPPERCASE="FUNCTION_UNARY_UPPERCASE";
    public static $FUNCTION_UNARY_LOWERCASE="FUNCTION_UNARY_LOWERCASE";
    public static $FUNCTION_UNARY_STRINGLENGTH="FUNCTION_UNARY_STRINGLENGTH";
    public static $FUNCTION_UNARY_ROUND="FUNCTION_UNARY_ROUND";
    public static $FUNCTION_UNARY_ISNULL="FUNCTION_UNARY_ISNULL";
    public static $FUNCTION_UNARY_NOT="FUNCTION_UNARY_NOT";
    public static $FUNCTION_UNARY_SIN="FUNCTION_UNARY_SIN";
    public static $FUNCTION_UNARY_COS="FUNCTION_UNARY_COS";
    public static $FUNCTION_UNARY_TAN="FUNCTION_UNARY_TAN";
    public static $FUNCTION_UNARY_ASIN="FUNCTION_UNARY_ASIN";
    public static $FUNCTION_UNARY_ACOS="FUNCTION_UNARY_ACOS";
    public static $FUNCTION_UNARY_ATAN="FUNCTION_UNARY_ATAN";
    public static $FUNCTION_UNARY_SQRT="FUNCTION_UNARY_SQRT";
    public static $FUNCTION_UNARY_ABS="FUNCTION_UNARY_ABS";
    public static $FUNCTION_UNARY_FLOOR="FUNCTION_UNARY_FLOOR";
    public static $FUNCTION_UNARY_CEIL="FUNCTION_UNARY_CEIL";
    public static $FUNCTION_UNARY_EXP="FUNCTION_BINARY_EXP";
    public static $FUNCTION_UNARY_LOG="FUNCTION_BINARY_LOG";
    public static $FUNCTION_UNARY_DATETIME_PARSE="FUNCTION_UNARY_DATETIME_PARSE";
    public static $FUNCTION_UNARY_DATETIME_DATEPART="FUNCTION_UNARY_DATETIME_DATEPART";
    
    public function __construct($kind, UniversalFilterNode $column=null) {
        parent::__construct($kind);
        if($column!=null) $this->setSource($column, 0);
    }
}

class DateTimeExtractConstants {
    private function __construct() {}
    public static $EXTRACT_SECOND="SECOND";
    public static $EXTRACT_MINUTE="MINUTE";
    public static $EXTRACT_HOUR="HOUR";
    public static $EXTRACT_DAY="DAY";
    public static $EXTRACT_WEEK="WEEK";
    public static $EXTRACT_MONTH="MONTH";
    public static $EXTRACT_YEAR="YEAR";
    public static $EXTRACT_MINUTE_SECOND="MINUTE_SECOND";//MINUTES:SECONDS
    public static $EXTRACT_HOUR_SECOND="HOUR_SECOND";//HOURS:MINUTES:SECONDS
    public static $EXTRACT_HOUR_MINUTE="HOUR_MINUTE";//HOURS:MINUTES
    public static $EXTRACT_DAY_SECOND="DAY_SECOND";//DAYS HOURS:MINUTES:SECONDS
    public static $EXTRACT_DAY_MINUTE="DAY_MINUTE";//DAYS HOURS:MINUTES
    public static $EXTRACT_DAY_HOUR="DAY_HOUR";//DAYS HOURS
    public static $EXTRACT_YEAR_MONTH="YEAR_MONTH";//YEARS-MONTHS
}

/**
 * This class represents all binary functions
 * 
 * type: (Column,Column) -> Column
 * type: (Cell, Cell) -> Cell
 */
class BinaryFunction extends NormalFilterNode {
    
    public static $FUNCTION_BINARY_PLUS="FUNCTION_BINARY_PLUS";
    public static $FUNCTION_BINARY_MINUS="FUNCTION_BINARY_MINUS";
    public static $FUNCTION_BINARY_MULTIPLY="FUNCTION_BINARY_MULTIPLY";
    public static $FUNCTION_BINARY_DIVIDE="FUNCTION_BINARY_DIVIDE";
    public static $FUNCTION_BINARY_COMPARE_EQUAL="FUNCTION_BINARY_COMPARE_EQUAL";
    public static $FUNCTION_BINARY_COMPARE_SMALLER_THAN="FUNCTION_BINARY_COMPARE_SMALLER_THAN";
    public static $FUNCTION_BINARY_COMPARE_LARGER_THAN="FUNCTION_BINARY_COMPARE_LARGER_THAN";
    public static $FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN="FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN";
    public static $FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN="FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN";
    public static $FUNCTION_BINARY_COMPARE_NOTEQUAL="FUNCTION_BINARY_COMPARE_NOTEQUAL";
    public static $FUNCTION_BINARY_OR="FUNCTION_BINARY_OR";
    public static $FUNCTION_BINARY_AND="FUNCTION_BINARY_AND";
    public static $FUNCTION_BINARY_MATCH_REGEX="FUNCTION_BINARY_MATCH_REGEX";// does $1 matches $2 ? ($2 is in php regex format!)
    public static $FUNCTION_BINARY_ATAN2="FUNCTION_BINARY_ATAN2";
    public static $FUNCTION_BINARY_LOG="FUNCTION_BINARY_LOG";
    public static $FUNCTION_BINARY_POW="FUNCTION_BINARY_POW";
    public static $FUNCTION_BINARY_CONCAT="FUNCTION_BINARY_CONCAT";
    public static $FUNCTION_BINARY_DATETIME_PARSE="FUNCTION_BINARY_DATETIME_PARSE";//time, php format
    public static $FUNCTION_BINARY_DATETIME_EXTRACT="FUNCTION_BINARY_DATETIME_EXTRACT";/*time, DateTimeExtractConstants*/
    public static $FUNCTION_BINARY_DATETIME_FORMAT="FUNCTION_BINARY_DATETIME_FORMAT";/*time, php format*/
    public static $FUNCTION_BINARY_DATETIME_DATEDIFF="FUNCTION_BINARY_DATETIME_DATEDIFF";
    
    public function __construct($kind, UniversalFilterNode $columnA=null, UniversalFilterNode $columnB=null) {
        parent::__construct($kind);
        if($columnA!=null) $this->setSource($columnA, 0);
        if($columnB!=null) $this->setSource($columnB, 1);
    }
    
    public function getSourceCount() {
        return 2;
    }
}

/**
 * This class represents all ternary functions
 * 
 * type: (Column,Column,Column) -> Column
 * type: (Cell, Cell, Cell) -> Cell
 */
class TernaryFunction extends NormalFilterNode {
    
    public static $FUNCTION_TERNARY_SUBSTRING="FUNCTION_TERNARY_SUBSTRING";//get part of $1 from index $2 with length $3
    public static $FUNCTION_TERNARY_REGEX_REPLACE="FUNCTION_TERNARY_REGEX_REPLACE";//replace $1 by $2 in $3
    public static $FUNCTION_TERNARY_DATETIME_DATEADD="FUNCTION_TERNARY_DATETIME_DATEADD";// (date, string, constant:DateTimeExtractConstant)  (DATE_ADD(date INTERVAL string constant))
    public static $FUNCTION_TERNARY_DATETIME_DATESUB="FUNCTION_TERNARY_DATETIME_DATESUB";// (date, string, constant:DateTimeExtractConstant)  (DATE_SUB(date INTERVAL string constant))
    
    public function __construct($kind, UniversalFilterNode $columnA=null, UniversalFilterNode $columnB=null, UniversalFilterNode $columnC=null) {
        parent::__construct($kind);
        if($columnA!=null) $this->setSource($columnA, 0);
        if($columnB!=null) $this->setSource($columnB, 1);
        if($columnC!=null) $this->setSource($columnC, 2);
    }
    
    public function getSourceCount() {
        return 3;
    }
}

/**
 * This class represents all aggregator functions
 * 
 * type: Column -> Cell
 */
class AggregatorFunction extends NormalFilterNode {
    
    public static $AGGREGATOR_AVG="AGGREGATOR_AVG";
    public static $AGGREGATOR_COUNT="AGGREGATOR_COUNT";
    public static $AGGREGATOR_FIRST="AGGREGATOR_FIRST";
    public static $AGGREGATOR_LAST="AGGREGATOR_LAST";
    public static $AGGREGATOR_MAX="AGGREGATOR_MAX";
    public static $AGGREGATOR_MIN="AGGREGATOR_MIN";
    public static $AGGREGATOR_SUM="AGGREGATOR_SUM";
    
    public function __construct($kind, UniversalFilterNode $column=null) {
        parent::__construct($kind);
        if($column!=null) $this->setSource($column);
    }
}



/*
 *  Other specific functions
 */
 
/**
 * Checks if the value is in a list of constants
 * type: [Cell, [Constant, ...]] -> Cell
 * type: [Column, [Constant, ...]] -> Column
 */
class CheckInFunction extends NormalFilterNode {
    private $constants;
    
    public static $FUNCTION_IN_LIST="FUNCTION_IN_LIST";// is a varargs function
    
    public function __construct(array /* of Constant */ $constants, UniversalFilterNode $source = null) {
        parent::__construct(CheckInFunction::$FUNCTION_IN_LIST);
        $this->constants=$constants;
        if($column!=null) $this->setSource($source);
    }
    
    public function getConstants(){
        return $this->constants;
    }
}


/**
 * Extre functions
 */
include_once("universalfilter/CombinedFilterGenerators.class.php");
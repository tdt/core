<?php

/**
 * This class converts a tree to a SQL query string (simple query)
 * This is based on the debugging TreeToString of the universalfilters
 *
 * IMPORTANT NOTE: the functions which contain "not supported yet" are meant for this converter
 * this doesnt mean that the functionality hasn't been implemented in the universalinterpreter!
 *
 * @package The-Datatank/universalfilter/tools
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\spectql\implementation\converter;

use tdt\core\spectql\implementation\universalfilters\AggregatorFunction;
use tdt\core\spectql\implementation\universalfilters\BinaryFunction;
use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\ColumnSelectionFilter;
use tdt\core\spectql\implementation\universalfilters\Constant;
use tdt\core\spectql\implementation\universalfilters\DataGrouper;
use tdt\core\spectql\implementation\universalfilters\DistinctFilter;
use tdt\core\spectql\implementation\universalfilters\FilterByExpressionFilter;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\LimitFilter;
use tdt\core\spectql\implementation\universalfilters\SortFieldsFilter;
use tdt\core\spectql\implementation\universalfilters\SortFieldsFilterColumn;
use tdt\core\spectql\implementation\universalfilters\TableAliasFilter;
use tdt\core\spectql\implementation\universalfilters\TernaryFunction;
use tdt\core\spectql\implementation\universalfilters\UnaryFunction;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

class SQLConverter
{

    private $sql = "";
    // the SELECT identifiers of the querynode
    private $identifiers = array();
    private $IN_SELECT_CLAUSE = TRUE;
    private $selectClausePresent = FALSE;
    private $headerNames;
    private $groupbyclause = array();
    private $orderbyclause = array();
    private $selectclause = array();
    private $whereclause = array();
    private $limitclause = array();

    /*
     * Returns an array with [0] => offset and [1] => limit (amount of records)
     */

    public function getLimitClause()
    {
        return $this->limitclause;
    }

    public function getOrderByClause()
    {
        return $this->orderbyclause;
    }

    /*
     * array of column AS alias entries
     */

    public function getSelectClause()
    {
        return $this->selectclause;
    }

    /*
     * array of identifier names
     */

    public function getGroupByClause()
    {
        return $this->groupbyclause;
    }

    /*
     * this is an array with just one string! i.e. a>b AND z<e
     * in future development we might use a new return structure.
     */

    public function getWhereClause()
    {
        return $this->whereclause;
    }

    public function __construct($headerNames)
    {
        $this->headerNames = $headerNames;
    }

    public function treeToSQLClauses(UniversalFilterNode $tree)
    {
        $this->treeToSQL($tree);
    }

    private function treeToSQL(UniversalFilterNode $tree)
    {
        $classname = get_class($tree);
        $method = "print_" . end(explode("\\", $classname));
        //calls the correct clone method and then returns.
        return $this->$method($tree);
    }

    private function print_LimitFilter(LimitFilter $filter)
    {
        $this->limitclause[0] = $filter->offset;
        $this->limitclause[1] = $filter->limit;
        $this->treeToSQL($filter->getSource());
    }

    private function print_Identifier(Identifier $filter)
    {

        if ($this->IN_SELECT_CLAUSE) {
            array_push($this->identifiers, $filter->getIdentifierString());
        }

        return $filter->getIdentifierString();
    }

    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    private function print_Constant(Constant $filter)
    {
        return $filter->getConstant();
    }

    private function print_TableAliasFilter(TableAliasFilter $filter)
    {
        // not implemented yet
    }

    private function print_SortFieldsFilter(SortFieldsFilter $filter)
    {

        foreach ($filter->getColumnData() as $index => $originalColumn) {
            $name = $originalColumn->getColumn()->getIdentifierString();
            $order = ($originalColumn->getSortOrder() == SortFieldsFilterColumn::$SORTORDER_ASCENDING ? "ASC" : "DESC");
            array_push($this->orderbyclause, $name . " " . $order);
        }

        // continue recursion
        $this->treeToSQL($filter->getSource());
    }

    private function print_FilterByExpressionFilter(FilterByExpressionFilter $filter)
    {

        $this->IN_SELECT_CLAUSE = FALSE;
        array_push($this->whereclause, $this->treeToSQL($filter->getExpression()));
    }

    private function print_ColumnSelectionFilter(ColumnSelectionFilter $filter)
    {

        $this->selectClausePresent = TRUE;

        foreach ($filter->getColumnData() as $index => $originalColumn) {


            if ($originalColumn->getColumn()->getType() == "IDENTIFIER" && $originalColumn->getColumn()->getIdentifierString() == "*") {

                array_push($this->identifiers, '*');
                foreach ($this->headerNames as $headerName) {
                    array_push($this->selectclause, "$headerName AS $headerName");
                }
            } else {
                $identifier = $this->treeToSQL($originalColumn->getColumn());

                $headerName = array_shift($this->headerNames);
                array_push($this->selectclause, "$identifier AS $headerName");
            }
        }


        if ($filter->getSource()->getType() != "IDENTIFIER") {
            // continue the recursion
            $this->treeToSQL($filter->getSource());
        }
    }

    private function print_DataGrouper(DataGrouper $filter)
    {
        foreach ($filter->getColumns() as $column) {
            array_push($this->groupbyclause, $column->getIdentifierString());
        }

        $this->treeToSQL($filter->getSource());
    }

    private function print_UnaryFunction(UnaryFunction $filter)
    {

        switch ($filter->getType()) {
            case UnaryFunction::$FUNCTION_UNARY_UPPERCASE:
                $function = "UPPER( ";
                $function.=$this->treeToSQL($filter->getSource(0));
                $function.= " ) ";
                return $function;
            case UnaryFunction::$FUNCTION_UNARY_LOWERCASE:
                $function = "LOWER( ";
                $function.=$this->treeToSQL($filter->getSource(0));
                $function.= " ) ";
                return $function;
            case UnaryFunction::$FUNCTION_UNARY_STRINGLENGTH:
                $function = "LEN( ";
                $function.=$this->treeToSQL($filter->getSource(0));
                $function.= " ) ";
                return $function;
            case UnaryFunction::$FUNCTION_UNARY_ROUND:
                $function = "ROUND( ";
                $function.=$this->treeToSQL($filter->getSource(0));
                $function.= " ) ";
                return $function;
            case UnaryFunction::$FUNCTION_UNARY_ISNULL:
                $function = "ISNULL( ";
                $function.= $this->treeToSQL($filter->getSource(0));
                $function.= " ) ";
                return $function;
            default:
                break;
        }
    }

    private function print_BinaryFunction(BinaryFunction $filter)
    {

        switch ($filter->getType()) {
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_EQUAL:
                $function = $this->treeToSQL($filter->getSource(0));
                $function.= "=";
                $function.= $this->treeToSQL($filter->getSource(1));
                return $function;
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_THAN:
                $function = $this->treeToSQL($filter->getSource(0));
                $function.= "<";
                $function.= $this->treeToSQL($filter->getSource(1));
                return $function;
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_THAN:
                $function = $this->treeToSQL($filter->getSource(0));
                $function.= ">";
                $function.= $this->treeToSQL($filter->getSource(1));
                return $function;
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN:
                $function = $this->treeToSQL($filter->getSource(0));
                $function.= ">=";
                $function.= $this->treeToSQL($filter->getSource(1));
                return $function;
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN:
                $function = $this->treeToSQL($filter->getSource(0));
                $function.= "<=";
                $function.= $this->treeToSQL($filter->getSource(1));
                return $function;
            case BinaryFunction::$FUNCTION_BINARY_COMPARE_NOTEQUAL:
                $function = $this->treeToSQL($filter->getSource(0));
                $function.= "!=";
                $function.= $this->treeToSQL($filter->getSource(1));
                return $function;
            case BinaryFunction::$FUNCTION_BINARY_AND:
                $function = $this->treeToSQL($filter->getSource(0));
                $function.= " AND ";
                $function.= $this->treeToSQL($filter->getSource(1));
                return $function;
            case BinaryFunction::$FUNCTION_BINARY_MATCH_REGEX:
                $function = $this->treeToSQL($filter->getSource(0));
                $function.= " LIKE ";
                $function.= $this->treeToSQL($filter->getSource(1));
                return $function;
            default:
                break;
        }
    }

    private function print_TernaryFunction(TernaryFunction $filter)
    {
        // not supported yet
    }

    private function print_AggregatorFunction(AggregatorFunction $filter)
    {

        switch ($filter->getType()) {
            case AggregatorFunction::$AGGREGATOR_COUNT:
                $function = " count( ";
                $function.= $this->treeToSQL($filter->getSource(0));
                $function.= ") ";
                return $function;
            case AggregatorFunction::$AGGREGATOR_AVG:
                $function = " avg( ";
                $function.= $this->treeToSQL($filter->getSource(0));
                $function.= ") ";
                return $function;
            case AggregatorFunction::$AGGREGATOR_FIRST:
                $function = " first( ";
                $function.= $this->treeToSQL($filter->getSource(0));
                $function.= ") ";
                return $function;
            case AggregatorFunction::$AGGREGATOR_LAST:
                $function = " last( ";
                $function.= $this->treeToSQL($filter->getSource(0));
                $function.= ") ";
                return $function;
            case AggregatorFunction::$AGGREGATOR_MAX:
                $function = " max( ";
                $function.= $this->treeToSQL($filter->getSource(0));
                $function.= ") ";
                return $function;
            case AggregatorFunction::$AGGREGATOR_MIN:
                $function = " min( ";
                $function.= $this->treeToSQL($filter->getSource(0));
                $function.= ") ";
                return $function;
            case AggregatorFunction::$AGGREGATOR_SUM:
                $function = " sum( ";
                $function.= $this->treeToSQL($filter->getSource(0));
                $function.= ") ";
                return $function;
            default:
                break;
        }
    }

    private function print_CheckInFunction(CheckInFunction $filter)
    {
        // not supported yet
    }

    /*
     * expects for treeToSQLClauses to be called first.
     */

    public function getPresentClauses()
    {
        $clauses = array();

        if ($this->whereclause) {
            array_push($clauses, "where");
        }

        if ($this->groupbyclause) {
            array_push($clauses, "groupby");
        }

        if ($this->selectclause) {
            array_push($clauses, "select");
        }

        if ($this->orderbyclause) {
            array_push($clauses, "orderby");
        }

        if ($this->limitclause) {
            array_push($clauses, "limit");
        }

        return $clauses;
    }

    /*
     * Returns the clause(s) for a given clause name. array(["clause"] => ... )
     * The possible clauses are where - groupby - select - order by
     * If a select clause is asked for , then the groupby and where clause, if applicable
     * will be returned also.
     */

    public function getClause($clause)
    {

        $clauses = array();

        foreach ($this->getPresentClauses() as $clause_name) {
            switch ($clause_name) {
                case "where":
                    array_push($clauses, $this->whereclause);
                    break;
                case "groupby":
                    array_push($clauses, $this->groupbyclause);
                    break;
                case "select":
                    array_push($clauses, $this->selectclause);
                    break;
                case "orderby":
                    array_push($clauses, $this->orderbyclause);
                    break;
                case "limit":
                    array_push($clauses, $this->limitclause);
                    break;
            }

            if ($clause == $clause_name)
                break;
        }
        return $clauses;
    }
}

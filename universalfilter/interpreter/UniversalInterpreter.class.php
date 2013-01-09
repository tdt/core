<?php

/**
 * The UniversalInterpreter: 
 * Create an instance of this class and give it a query-tree execute the filter.
 *
 * @package The-Datatank/universalfilter/interpreter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
include_once("core/universalfilter/interpreter/IInterpreterControl.class.php");

include_once("core/universalfilter/interpreter/executers/UniversalFilterExecuters.php");

namespace tdt\core\universalfilter\interpreter;

class UniversalInterpreter implements tdt\core\universalfilter\interpreter\IInterpreterControl {

    private $executers;
    private $tablemanager;

    /**
     * Are nested querys allowed?
     * true = yes, they are allowed.
     * false = no, throw an exception if you try to use them...
     * 
     * @var boolean 
     */
    public static $ALLOW_NESTED_QUERYS = false;

    /**
     * For debugging purposses, would you like to see debug information about execution of querys on the source?
     * @var boolean 
     */
    public static $DEBUG_QUERY_ON_SOURCE_EXECUTION = false;

    /**
     * How the date is saved internally...
     * @var string 
     */
    public static $INTERNAL_DATETIME_FORMAT = 'Y-m-d H:i:s';
    public static $INTERNAL_DATETIME_FORMAT_ONLYDATE = "Y-m-d";
    public static $INTERNAL_DATETIME_FORMAT_ONLYTIME = "H:i:s";

    /**
     * Constructor, fill the executer-class map.
     */
    public function __construct($tablemanager) {
        /*
          AutoInclude::register("Environment","cores/core/universalfilter/interpreter/Environment.class.php");
          AutoInclude::register("DummyUniversalFilterNode","cores/core/universalfilter/interpreter/other/DummyUniversalFilterNode.class.php");
          AutoInclude::register("SourceUsageData","cores/core/universalfilter/interpreter/sourceusage/SourceUsageData.class.php");
          AutoInclude::register("ExpectedHeaderNamesAttachment","cores/core/universalfilter/sourcefilterbinding/ExpectedHeaderNamesAttachment.class.php");
          AutoInclude::register("FilterTreeCloner","cores/core/universalfilter/interpreter/cloning/FilterTreeCloner.class.php");
          AutoInclude::register("UniversalOptimizer","cores/core/universalfilter/interpreter/optimizer/UniversalOptimizer.class.php");
          AutoInclude::register("TreePrinter","cores/core/universalfilter/interpreter/debugging/TreePrinter.class.php");
         */
        $this->tablemanager = $tablemanager;

        $this->executers = array(
            "IDENTIFIER" => "IdentifierExecuter",
            "CONSTANT" => "ConstantExecuter",
            "FILTERCOLUMN" => "ColumnSelectionFilterExecuter",
            "FILTERSORTCOLUMNS" => "SortFieldsFilterExecuter",
            "FILTEREXPRESSION" => "FilterByExpressionExecuter",
            "DATAGROUPER" => "DataGrouperExecuter",
            "JOIN" => "DatasetJoinFilterExecuter",
            "TABLEALIAS" => "TableAliasExecuter",
            "FILTERDISTINCT" => "DistinctFilterExecuter",
            "FILTERLIMIT" => "LimitFilterExecuter",
            "EXTERNALLY_CALCULATED_NODE" => "ExternallyCalculatedFilterNodeExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_UPPERCASE => "UnaryFunctionUppercaseExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_LOWERCASE => "UnaryFunctionLowercaseExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_STRINGLENGTH => "UnaryFunctionStringLengthExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_ROUND => "UnaryFunctionRoundExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_ISNULL => "UnaryFunctionIsNullExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_NOT => "UnaryFunctionNotExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_SIN => "UnaryFunctionSinExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_COS => "UnaryFunctionCosExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_TAN => "UnaryFunctionTanExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_ASIN => "UnaryFunctionAsinExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_ACOS => "UnaryFunctionAcosExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_ATAN => "UnaryFunctionAtanExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_SQRT => "UnaryFunctionSqrtExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_ABS => "UnaryFunctionAbsExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_FLOOR => "UnaryFunctionFloorExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_CEIL => "UnaryFunctionCeilExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_EXP => "UnaryFunctionExpExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_LOG => "UnaryFunctionLogExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_DATETIME_DATEPART => "UnaryFunctionDatePartExecuter",
            tdt\core\universalfilter\UnaryFunction::$FUNCTION_UNARY_DATETIME_PARSE => "UnaryFunctionParseDateTimeExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_PLUS => "BinaryFunctionPlusExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MINUS => "BinaryFunctionMinusExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MULTIPLY => "BinaryFunctionMultiplyExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_DIVIDE => "BinaryFunctionDivideExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_EQUAL => "BinaryFunctionEqualityExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_THAN => "BinaryFunctionSmallerExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_THAN => "BinaryFunctionLargerExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN => "BinaryFunctionSmallerEqualExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN => "BinaryFunctionLargerEqualExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_COMPARE_NOTEQUAL => "BinaryFunctionNotEqualExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_OR => "BinaryFunctionOrExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_AND => "BinaryFunctionAndExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_ATAN2 => "BinaryFunctionAtan2Executer",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_LOG => "BinaryFunctionLogExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_POW => "BinaryFunctionPowExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_MATCH_REGEX => "BinaryFunctionMatchRegexExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_CONCAT => "BinaryFunctionConcatExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_DATETIME_PARSE => "BinaryFunctionDateTimeParseExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_DATETIME_EXTRACT => "BinaryFunctionDateTimeExtractExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_DATETIME_FORMAT => "BinaryFunctionDateTimeFormatExecuter",
            tdt\core\universalfilter\BinaryFunction::$FUNCTION_BINARY_DATETIME_DATEDIFF => "BinaryFunctionDateTimeDateDiffExecuter",
            tdt\core\universalfilter\TernaryFunction::$FUNCTION_TERNARY_SUBSTRING => "TernaryFunctionSubstringExecuter",
            tdt\core\universalfilter\TernaryFunction::$FUNCTION_TERNARY_REGEX_REPLACE => "TernaryFunctionRegexReplacementExecuter",
            tdt\core\universalfilter\TernaryFunction::$FUNCTION_TERNARY_DATETIME_DATEADD => "TernaryFunctionDateTimeDateAddExecuter",
            tdt\core\universalfilter\TernaryFunction::$FUNCTION_TERNARY_DATETIME_DATESUB => "TernaryFunctionDateTimeDateSubExecuter",
            tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_AVG => "AverageAggregatorExecuter",
            tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_COUNT => "CountAggregatorExecuter",
            tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_FIRST => "FirstAggregatorExecuter",
            tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_LAST => "LastAggregatorExecuter",
            tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_MAX => "MaxAggregatorExecuter",
            tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_MIN => "MinAggregatorExecuter",
            tdt\core\universalfilter\AggregatorFunction::$AGGREGATOR_SUM => "SumAggregatorExecuter",
            tdt\core\universalfilter\CheckInFunction::$FUNCTION_IN_LIST => "CheckInFunctionExecuter"
        );
    }

    public function findExecuterFor(tdt\core\universalfilter\UniversalFilterNode $filternode) {
        return new $this->executers[$filternode->getType()]();
    }

    public function getTableManager() {
        return $this->tablemanager;
    }

    public function interpret(tdt\core\universalfilter\UniversalFilterNode $originaltree) {
        //var_dump($originaltree);
        if (tdt\core\universalfilter\interpreter\UniversalInterpreter::$DEBUG_QUERY_ON_SOURCE_EXECUTION) {
            $printer = new tdt\core\universalfilter\interpreter\debugging\TreePrinter();
            echo "<h2>Original Query:</h2>";
            $printer->printString($originaltree);
        }

        //CLONE QUERY (because we will modify it... and the caller might want to keep the original query)
        $cloner = new tdt\core\universalfilter\interpreter\cloning\FilterTreeCloner();
        $clonedtree = $cloner->deepCopyTree($originaltree);

       
        $tree = $clonedtree;


        //INITIAL ENVIRONMENT... is empty
        $emptyEnv = new tdt\core\universalfilter\interpreter\Environment();
        $emptyEnv->setTable(new tdt\core\universalfilter\data\UniversalFilterTable(new tdt\core\universalfilter\UniversalFilterTableHeader(array(), true, false), new tdt\core\universalfilter\data\UniversalFilterTableContent()));


        //CALCULATE HEADER FIRST TIME + QUERY SYNTAX DETECTION
        // calculate the header already once on the original query.
        // it can throw errors...
        $executer = $this->findExecuterFor($tree);
        $executer->initExpression($tree, $emptyEnv, $this, false);


        //EXECUTE PARTS ON SOURCE
        // - modify the headers to include column names
        $executer->modififyFiltersWithHeaderInformation();

        // - calculate single source usages
        $rootDummyFilter = new tdt\core\universalfilter\interpreter\other\DummyUniversalFilterNode($tree);
        $singleSourceUsages = $executer->filterSingleSourceUsages($rootDummyFilter, 0);

        // - calculated... now execute them on the sources... AND BUILD A NEW QUERY
        foreach ($singleSourceUsages as $singleSource) {
            // - unpack data
            $filterSourceNode = $singleSource->getFilterSourceNode();
            $filterParentNode = $singleSource->getFilterParentNode();
            $filterParentSourceIndex = $singleSource->getFilterParentSourceIndex();
            $sourceId = $singleSource->getSourceId();

            // debug
            if (tdt\core\universalfilter\interpreter\UniversalInterpreter::$DEBUG_QUERY_ON_SOURCE_EXECUTION) {
                $printer = new tdt\core\universalfilter\interpreter\debugging\TreePrinter();
                echo "<h2>This is given to the source with id \"" . $sourceId . "\":</h2>";
                $printer->printString($filterSourceNode);
            }

            // - do it
            $newQuery = $this->getTableManager()->runFilterOnSource($filterSourceNode, $sourceId);
            $filterParentNode->setSource($newQuery, $filterParentSourceIndex);
        }


        $tree = $rootDummyFilter->getSource();


        //EXECUTE (for real this time)
        $executer = $this->findExecuterFor($tree);
        $executer->initExpression($tree, $emptyEnv, $this, false);
        get_class($executer);

        //get the table, in two steps
        $header = $executer->getExpressionHeader();

        $content = $executer->evaluateAsExpression();

        // externallycal. doesn't do anything on clean up so it doesn't need it
        if (get_class($executer) != "ExternallyCalculatedFilterNodeExecuter") {
            $executer->cleanUp();
        }

        //RETURN
        return new UniversalFilterTable($header, $content);

        //CLEANUP -> when you don't need the data anymore
        //$content->tryDestroyTable();
    }

}

?>

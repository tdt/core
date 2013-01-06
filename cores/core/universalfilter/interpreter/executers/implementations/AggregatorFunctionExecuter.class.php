<?php

/**
 * This file contains the abstact top class for all aggregators
 * 
 * The filter inside the aggregator gets executed row by row
 *
 * @package The-Datatank/universalfilter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
abstract class AggregatorFunctionExecuter extends AbstractUniversalFilterNodeExecuter {

    protected $header;
    protected $header1;
    protected $singleColumnSingleRow;
    //private
    private $executer1;
    private $evaluatorTable;
    private $topenv;
    private $typeInlineSelect;

    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn) {
        $this->filter = $filter;

        $this->executer1 = $interpreter->findExecuterFor($this->filter->getSource());


        //
        // Evaluate the header of the filter inside this aggregator...
        //  (evaluation need to be done for each row)
        //
        
        //check if header1 returns isSingleRow if we give it a single row
        $evaluatorEnvironment = $topenv->newModifiableEnvironment();
        //single row header
        $evaluatorHeader = $topenv->getTable()->getHeader()->cloneHeader();
        $evaluatorHeader->setIsSingleRowByConstruction(true);
        //single row content
        $evaluatorContent = new UniversalFilterTableContent();
        $evaluatorContent->addRow(new UniversalFilterTableContentRow());
        //single row table
        $this->evaluatorTable = new UniversalFilterTable($evaluatorHeader, $evaluatorContent);
        //single row environment
        $evaluatorEnvironment->setTable($this->evaluatorTable);

        //init executer
        $this->executer1->initExpression($this->filter->getSource(), $evaluatorEnvironment, $interpreter, true);

        //check executer header
        $evaluatedHeader = $this->executer1->getExpressionHeader();
        $this->typeInlineSelect = !$evaluatedHeader->isSingleRowByConstruction();
        if ($this->typeInlineSelect) {
            if (!UniversalInterpreter::$ALLOW_NESTED_QUERYS) {
                throw new Exception("Nested Query's are disabled because of performance issues.");
            }
            if (!$evaluatedHeader->isSingleColumnByConstruction()) {
                if (!$this->combinesMultipleColumns()) {
                    throw new Exception("If you use a columnSelectionFilter in a Aggregator, the columnSelectionFilter should only return 1 column.");
                }
            }
        }

        // header for the executer, as seen by the classes that override this class. (what you would expect as header)
        // not the same as the evaluatedHeader, as we execute it row by row...
        $singleRow = $topenv->getTable()->getHeader()->isSingleRowByConstruction();
        $globalHeader = $evaluatedHeader->cloneHeader();
        $globalHeader->setIsSingleRowByConstruction($singleRow);
        if ($this->typeInlineSelect) {//special header for inline select...
            $newColumns = array();

            for ($columnIndex = 0; $columnIndex < $globalHeader->getColumnCount(); $columnIndex++) {
                $columnId = $globalHeader->getColumnIdByIndex($columnIndex);
                $groupedHeaderColumn = $globalHeader->getColumnInformationById($columnId)->cloneColumnGrouped();

                array_push($newColumns, $groupedHeaderColumn);
            }
            $globalHeader = new UniversalFilterTableHeader($newColumns, $singleRow, true);
        }

        //set the seen header
        $this->header1 = $globalHeader;

        //save context for content-generation
        $this->topenv = $topenv;


        //
        // - now calculate own header
        //
        if ($this->header1->isSingleColumnByConstruction()) {
            //single column, may be grouped...
            $singleRow = true;


            $columnId = $this->header1->getColumnId();
            $columnInfo = $this->header1->getColumnInformationById($columnId);
            $columnName = $columnInfo->getName();

            if ($columnInfo->isGrouped()) {
                $singleRow = false;
            }

            $cominedHeaderColumn = null;
            if ($this->keepFullInfo()) {
                $cominedHeaderColumn = $columnInfo->cloneColumnInfo();
            } else {
                $combinedName = $this->getName($columnName);
                $cominedHeaderColumn = $columnInfo->cloneBaseUpon($combinedName);
            }
            $newColumns = array($cominedHeaderColumn);


            $this->header = new UniversalFilterTableHeader($newColumns, $singleRow, true);
            $this->singleColumnSingleRow = $singleRow;
        } else {
            //multiple columns -> grouping not allowed!

            $newColumns = array();
            if (!$this->combinesMultipleColumns()) {
                for ($index = 0; $index < $this->header1->getColumnCount(); $index++) {
                    $columnId = $this->header1->getColumnIdByIndex($index);
                    $columnInfo = $this->header1->getColumnInformationById($columnId);
                    $columnName = $columnInfo->getName();

                    if ($columnInfo->isGrouped()) {
                        //Should never happen?
                        throw new Exception("This operation can not be used on multiple columns with grouping.");
                    }

                    $cominedHeaderColumn = null;
                    if ($this->keepFullInfo()) {
                        $cominedHeaderColumn = $columnInfo->cloneColumnInfo();
                    } else {
                        $combinedName = $this->getName($columnName);
                        $cominedHeaderColumn = $columnInfo->cloneBaseUpon($combinedName);
                    }
                    array_push($newColumns, $cominedHeaderColumn);
                }
            } else {
                $newColumns = array(new UniversalFilterTableHeaderColumnInfo(array($this->getName("_multiple_columns_"))));
            }

            $this->header = new UniversalFilterTableHeader($newColumns, true, $this->combinesMultipleColumns());
        }
    }

    /**
     * Evaluates the subfilter for each row. (neccessary for SELECTS in AVG in SELECT)
     * 
     * @return UniversalFilterTableContent 
     */
    protected function evaluateSubExpression() {
        $context = $this->topenv->getTable()->getContent();
        $evaluatedHeader = $this->executer1->getExpressionHeader();

        $newContent = new UniversalFilterTableContent();

        for ($index = 0; $index < $context->getRowCount(); $index++) {

            $contextRow = $context->getRow($index);

            $this->evaluatorTable->getContent()->setRow(0, $contextRow);

            $executedContent = $this->executer1->evaluateAsExpression();

            if (!$this->typeInlineSelect) {
                $newContent->addRow($executedContent->getRow(0));
            } else {
                $newRow = new UniversalFilterTableContentRow();
                for ($columnIndex = 0; $columnIndex < $this->header1->getColumnCount(); $columnIndex++) {
                    $newColumnId = $this->header1->getColumnIdByIndex($columnIndex);
                    $oldColumnId = $evaluatedHeader->getColumnIdByIndex($columnIndex);

                    $groupedContent = new UniversalFilterTableContent();
                    for ($execContentIndex = 0; $execContentIndex < $executedContent->getRowCount(); $execContentIndex++) {
                        $groupRow = new UniversalFilterTableContentRow();
                        $groupRow->defineValue("data", $executedContent->getRow($execContentIndex)->getCellValue($oldColumnId, true)); //crashes if grouped...
                    }

                    $newRow->defineGroupedValue($newColumnId, $groupedContent);
                }

                $newContent->addRow($newRow);
            }
            $executedContent->tryDestroyTable();
        }

        return $newContent;
    }

    public function getExpressionHeader() {
        return $this->header;
    }

    public function evaluateAsExpression() {
        $oldContent = $this->evaluateSubExpression();
        $newContent = new UniversalFilterTableContent();

        if ($this->header1->isSingleColumnByConstruction()) {
            $sourceColumnId = $this->header1->getColumnId();
            $finalid = $this->header->getColumnId();

            if ($this->singleColumnSingleRow) {
                //single column - not grouped
                $row = new UniversalFilterTableContentRow();
                $row->defineValue($finalid, $this->doCalculate($oldContent, $sourceColumnId));

                $newContent->addRow($row);
            } else {
                //single column - grouped
                for ($index = 0; $index < $oldContent->getRowCount(); $index++) {
                    //row
                    $row = $oldContent->getRow($index);

                    $newRow = new UniversalFilterTableContentRow();
                    $newRow->defineValue($finalid, $this->doCalculate($row->getGroupedValue($sourceColumnId), "data"));

                    $newContent->addRow($newRow);
                }
            }
        } else {
            //multiple columns - not grouped
            $newRow = new UniversalFilterTableContentRow();
            if (!$this->combinesMultipleColumns()) {
                //do each one on its own
                for ($index = 0; $index < $this->header1->getColumnCount(); $index++) {
                    $columnId = $this->header1->getColumnIdByIndex($index);
                    $columnInfo = $this->header1->getColumnInformationById($columnId);

                    $finalid = $this->header->getColumnIdByIndex($index);

                    $newRow->defineValue($finalid, $this->doCalculate($oldContent, $columnId));
                }
                $newContent->addRow($newRow);
            } else {
                //combine all (count)
                $finalid = $this->header->getColumnId();

                $newRow = new UniversalFilterTableContentRow();
                $newRow->defineValue($finalid, $this->calculateValue($oldContent, null));

                $newContent->addRow($newRow);
            }
        }

        $oldContent->tryDestroyTable();

        return $newContent;
    }

    private function doCalculate(UniversalFilterTableContent $content, $columnId) {
        if ($this->errorIfNoItems()) {
            if ($content->getRowCount() == 0) {
                throw new Exception("This aggregator can not be applied to an empty column.");
            }
        }
        return $this->calculateValue($content, $columnId);
    }

    public function cleanUp() {
        try {
            $this->executer1->cleanUp();

            $this->evaluatorTable->getContent()->tryDestroyTable();
        } catch (Exception $ex) {
            
        }
    }

    public function modififyFiltersWithHeaderInformation() {
        parent::modififyFiltersWithHeaderInformation();
        $this->executer1->modififyFiltersWithHeaderInformation();
    }

    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex) {
        $arr = $this->executer1->filterSingleSourceUsages($this->filter, 0);

        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }

    /**
     * Converts a column to an array to make it easier to process 
     * 
     * CAN CONTAIN NULL VALUES!
     * 
     * @todo TODO: What if big table => should NOT convert to array. => Rewrite all aggregators... (can not use array_sum, count, max, min, ...)
     * @param UniversalFilterTableContent $content
     * @param type $columnId
     * @return array 
     */
    public function convertColumnToArray(UniversalFilterTableContent $content, $columnId) {
        $arr = array();
        for ($index = 0; $index < $content->getRowCount(); $index++) {
            array_push($arr, $content->getRow($index)->getCellValue($columnId, true));
        }
        return $arr;
    }

    //
    // (Most of) these methods need to be overriden by subclasses
    //
    
    public function getName($name) {
        return $name;
    }

    public function calculateValue(UniversalFilterTableContent $content, $columnId) {
        return 0;
    }

    public function keepFullInfo() {
        return true;
    }

    public function errorIfNoItems() {
        return false;
    }

    public function combinesMultipleColumns() {
        return false;
    }

}

?>

<?php

/**
 * Executes the ColumnSelectionFilter filter
 *
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\data\UniversalFilterTable;
use tdt\core\spectql\implementation\data\UniversalFilterTableContent;
use tdt\core\spectql\implementation\data\UniversalFilterTableContentRow;
use tdt\core\spectql\implementation\data\UniversalFilterTableHeader;
use tdt\core\spectql\implementation\interpreter\Environment;
use tdt\core\spectql\implementation\interpreter\executers\base\BaseEvaluationEnvironmentFilterExecuter;
use tdt\core\spectql\implementation\interpreter\IInterpreterControl;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

class ColumnSelectionFilterExecuter extends BaseEvaluationEnvironmentFilterExecuter
{

    private $returnsSingleRow;
    private $returnsSingleColumn;
    private $columnInterpreters;
    private $columnExecuters;
    private $header;
    private $executer;
    private $childEnvironmentData;
    private $giveToColumnsEnvironment;

    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn)
    {

        $this->filter = $filter;

        //get source environment header
        $executer = $interpreter->findExecuterFor($filter->getSource());
        $this->executer = $executer;


        $this->childEnvironmentData = $this->initChildEnvironment($filter, $topenv, $interpreter, $executer, false);
        $this->giveToColumnsEnvironment = $this->getChildEnvironment($this->childEnvironmentData);

        // Get the columns to filter
        $this->columnInterpreters = $filter->getColumnData();

        // Header information
        $this->returnsSingleRow = true;
        $this->returnsSingleColumn = true;
        $this->columnExecuters = array();

        // The columns for the returned table:
        $columns = array();

        // Loop all column-interpreters
        foreach ($this->columnInterpreters as $column) {

            $filterColumn = $column->getColumn();

            $exprexec = $interpreter->findExecuterFor($filterColumn);

            // Save the executer
            array_push($this->columnExecuters, $exprexec);

            // Init expression
            $exprexec->initExpression($filterColumn, $this->giveToColumnsEnvironment, $interpreter, true);
            $header = $exprexec->getExpressionHeader();

            // Header info
            if (!$header->isSingleRowByConstruction()) {
                $this->returnsSingleRow = false;
            }
            if (!$header->isSingleColumnByConstruction()) {
                $this->returnsSingleColumn = false;
            }

            $columnAlias = $column->getAlias();

            for ($resultColumnIndex = 0; $resultColumnIndex < $header->getColumnCount(); $resultColumnIndex++) {

                // Column information
                $columnId = $header->getColumnIdByIndex($resultColumnIndex);
                $columnInfo = $header->getColumnInformationById($columnId)->cloneColumnInfo();

                // Set column alias
                if ($columnAlias != null) {
                    if ($header->isSingleColumnByConstruction()) {
                        $columnInfo->aliasColumn($columnAlias);
                    } else {
                        throw new \Exception("Column-alias not supported for * - token");
                    }
                }

                //add the new column
                array_push($columns, $columnInfo);
            }
        }

        $this->header = new UniversalFilterTableHeader($columns, $this->returnsSingleRow, count($this->columnInterpreters) == 1 && $this->returnsSingleColumn);
    }

    public function getExpressionHeader()
    {
        return $this->header;
    }

    public function evaluateAsExpression()
    {

        $sourceheader = $this->executer->getExpressionHeader();
        $sourcecontent = $this->executer->evaluateAsExpression();

        $this->finishChildEnvironment($this->childEnvironmentData);
        $this->giveToColumnsEnvironment->setTable(new UniversalFilterTable($sourceheader, $sourcecontent));

        //create a new empty output table
        $newRows = new UniversalFilterTableContent();

        if (!$this->returnsSingleRow) {
            for ($index = 0; $index < $sourcecontent->getRowCount(); $index++) {
                $newRows->addRow(new UniversalFilterTableContentRow());
            }
        } else {
            $newRows->addRow(new UniversalFilterTableContentRow());
        }

        //loop all columnInterpreters
        foreach ($this->columnInterpreters as $columnIndex => $column) {

            //this column
            $filterColumn = $column->getColumn();

            //get executer
            $exprexec = $this->columnExecuters[$columnIndex];

            //get header (again)
            $header = $exprexec->getExpressionHeader();

            //evaluate
            $answer = $exprexec->evaluateAsExpression();

            for ($resultColumnIndex = 0; $resultColumnIndex < $header->getColumnCount(); $resultColumnIndex++) {

                // column information
                $columnId = $header->getColumnIdByIndex($resultColumnIndex);

                if ($header->isSingleRowByConstruction()) {

                    //copy single value to all rows
                    $rowValue = $answer->getRow(0);

                    for ($index = 0; $index < $newRows->getRowCount(); $index++) {
                        $rowValue->copyValueTo($newRows->getRow($index), $columnId, $columnId);
                    }
                } else {

                    //copy values to corresponding rows
                    for ($index = 0; $index < $newRows->getRowCount(); $index++) {
                        $rowValue = $answer->getRow($index);
                        $rowValue->copyValueTo($newRows->getRow($index), $columnId, $columnId);
                    }
                }
            }

            $answer->tryDestroyTable();
        }

        $sourcecontent->tryDestroyTable();

        return $newRows;
    }

    public function cleanUp()
    {
        try {
            $this->executer->cleanUp();

            foreach ($this->columnInterpreters as $columnIndex => $column) {
                //get executer
                $exprexec = $this->columnExecuters[$columnIndex];
                $exprexec->cleanUp();
            }
        } catch (Exception $ex) {

        }
    }

    public function modififyFiltersWithHeaderInformation()
    {

        parent::modififyFiltersWithHeaderInformation();
        $this->executer->modififyFiltersWithHeaderInformation();

        foreach ($this->columnInterpreters as $columnIndex => $column) {
            $exprexec = $this->columnExecuters[$columnIndex];
            $exprexec->modififyFiltersWithHeaderInformation();
        }
    }

    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex)
    {

        $arr = $this->executer->filterSingleSourceUsages($this->filter, 0);

        foreach ($this->columnInterpreters as $columnIndex => $column) {

            $exprexec = $this->columnExecuters[$columnIndex];
            $arr = array_merge($arr, $exprexec->filterSingleSourceUsages($this->filter, -1));
        }

        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }

}
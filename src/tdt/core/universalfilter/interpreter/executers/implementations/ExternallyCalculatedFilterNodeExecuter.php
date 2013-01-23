<?php

/**
 * Executes a filter that is calculated externally...
 *
 * @package The-Datatank/universalfilter/interpreter/executers/base
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\data\UniversalFilterTableHeader;
use tdt\core\universalfilter\interpreter\Environment;
use tdt\core\universalfilter\interpreter\executers\base\AbstractUniversalFilterNodeExecuter;
use tdt\core\universalfilter\interpreter\IInterpreterControl;
use tdt\core\universalfilter\interpreter\sourceusage\SourceUsageData;
use tdt\core\universalfilter\sourcefilterbinding\ExpectedHeaderNamesAttachment;
use tdt\core\universalfilter\UniversalFilterNode;

class ExternallyCalculatedFilterNodeExecuter extends AbstractUniversalFilterNodeExecuter {

    private $header;
    private $executer;

    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn) {
        // save the filter
        $this->filter = $filter;

        // get executer for source
        $source = $this->filter->getSource();
        $this->executer = $interpreter->findExecuterFor($source);
        $this->executer->initExpression($source, $topenv, $interpreter, $preferColumn);

        // the original header
        $originalheader = $this->executer->getExpressionHeader();
        // get the expected column names


        /*
         * Somewhere along the line the header name attachment goes missing
         * when you call upon a limit filter on top of a partially executed tree.
         * We dont get the headernames from the attachment, but from the originalheader itself.
         * We've only found this error to be occuring at a limit filter, so headernames shouldn't get changed.
         */
        $expectedheadernames = array();

        $attachment = $source->getAttachment(ExpectedHeaderNamesAttachment::$ATTACHMENTID);
        if (!is_null($attachment)) {
            $expectedheadernames = $attachment->getExpectedHeaderNames();
        } else {
            for ($i = 0; $i < $originalheader->getColumnCount(); $i++) {
                $id = $originalheader->getColumnIdByIndex($i);
                $name = $originalheader->getColumnNameById($id);
                array_push($expectedheadernames, $name);
            }
        }

        // get the calculated table
        $table = $this->filter->getTable();

        // make new header
        $newColumns = array();
        for ($index = 0; $index < $originalheader->getColumnCount(); $index++) {
            $columnId = $originalheader->getColumnIdByIndex($index);
            $columnInfo = $originalheader->getColumnInformationById($columnId);

            $givenName = $expectedheadernames[$index];
            $givenColumnId = $table->getHeader()->getColumnIdByName($givenName);
            if ($givenColumnId === null) {
                //show a more complete error message
                //display all columns in the returned table.
                $givenColumnsString = "";
                for ($givenColumnsIndex = 0; $givenColumnsIndex < $table->getHeader()->getColumnCount(); $givenColumnsIndex++) {
                    $columnIdGivenTable = $table->getHeader()->getColumnIdByIndex($givenColumnsIndex);
                    $columnNameGivenTable = $table->getHeader()->getColumnNameById($columnIdGivenTable);
                    if ($givenColumnsIndex != 0) {
                        $givenColumnsString.=", ";
                    }
                    $givenColumnsString.="" . $columnNameGivenTable . "";
                }

                throw new Exception("Illegal external calculation. The returned table should contain a column with name " . $givenName . ", but no column with that name found. Found columnNames: " . $givenColumnsString . ".");
            }

            $newHeaderColumn = $columnInfo->cloneColumnWithId($givenColumnId);
            array_push($newColumns, $newHeaderColumn);
        }

        // single row? single column?
        $isSingleRowByConstruction = $originalheader->isSingleRowByConstruction();
        $isSingleColumnByConstruction = $originalheader->isSingleColumnByConstruction();

        // check if data contains indeed a single row
        if ($isSingleRowByConstruction && $table->getContent()->getRowCount() != 1) {
            throw new Exception("Illegal external calculation. The returned table should contain one row.");
        }

        //new Header
        $this->header = new UniversalFilterTableHeader($newColumns, $isSingleRowByConstruction, $isSingleColumnByConstruction);



        $this->executer->cleanUp();
    }

    public function getExpressionHeader() {
        return $this->header;
    }

    public function evaluateAsExpression() {
        return $this->filter->getTable()->getContent();
    }

    public function cleanUp() {
        try {
            $this->executer->cleanUp();
        } catch (Exception $ex) {
            
        }
    }

    public function modififyFiltersWithHeaderInformation() {
        //do nothing
    }

    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex) {
        return array(new SourceUsageData($this->filter, $this->filter, NULL, "CAN_NOT_BE_EXECUTED_EXTERNALLY"));
    }

}

?>

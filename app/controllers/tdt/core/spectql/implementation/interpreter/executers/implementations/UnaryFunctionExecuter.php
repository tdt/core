<?php

/**
 * This file contains the abstact top class for all evaluators for unary functions
 *
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\data\UniversalFilterTableContent;
use tdt\core\spectql\implementation\data\UniversalFilterTableContentRow;
use tdt\core\spectql\implementation\data\UniversalFilterTableHeader;
use tdt\core\spectql\implementation\data\UniversalFilterTableHeaderColumnInfo;
use tdt\core\spectql\implementation\interpreter\Environment;
use tdt\core\spectql\implementation\interpreter\executers\base\AbstractUniversalFilterNodeExecuter;
use tdt\core\spectql\implementation\interpreter\IInterpreterControl;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

class UnaryFunctionExecuter extends AbstractUniversalFilterNodeExecuter
{

    private $header;
    private $executer1;
    private $header1;

    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn) {

        $this->filter = $filter;

        $this->executer1 = $interpreter->findExecuterFor($this->filter->getSource());

        //init
        $this->executer1->initExpression($this->filter->getSource(), $topenv, $interpreter, true);

        $this->header1 = $this->executer1->getExpressionHeader();

        //combined name
        $combinedName = $this->getName(
                $this->header1->getColumnNameById($this->header1->getColumnId()));

        //column
        $cominedHeaderColumn = new UniversalFilterTableHeaderColumnInfo(array($combinedName));

        //single row?
        $isSingleRowByConstruction = $this->header1->isSingleRowByConstruction();

        //new Header
        $this->header = new UniversalFilterTableHeader(array($cominedHeaderColumn), $isSingleRowByConstruction, true);
    }

    public function getExpressionHeader() {
        return $this->header;
    }

    public function evaluateAsExpression() {

        $table1content = $this->executer1->evaluateAsExpression();

        $idA = $this->header1->getColumnId();
        $finalid = $this->header->getColumnId();

        $rows = new UniversalFilterTableContent();

        $size = $table1content->getRowCount();

        // Loop through all rows and evaluate the expression
        for ($i = 0; $i < $size; $i++) {

            $row = new UniversalFilterTableContentRow();

            // Get the value for index i
            $valueA = $table1content->getValue($idA, $i, true);

            // Evaluate
            $value = $this->doUnaryFunction($valueA);

            $row->defineValue($finalid, $value);

            $rows->addRow($row);
        }

        $table1content->tryDestroyTable();

        //return the result
        return $rows;
    }

    public function getName($name) {
        return $name;
    }

    public function doUnaryFunction($value) {
        return null;
    }

    public function cleanUp() {
        try {
            $this->executer1->cleanUp();
        } catch (Exception $ex) {

        }
    }

    public function modififyFiltersWithHeaderInformation() {
        parent::modififyFiltersWithHeaderInformation();
        $this->executer1->modififyFiltersWithHeaderInformation();
    }

    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex) {
        $arr = $this->executer1->filterSingleSourceUsages($this->filter, 1);

        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }

}
<?php

/**
 * Executes the LimitFilter filter
 *
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\data\UniversalFilterTableContent;
use tdt\core\spectql\implementation\interpreter\Environment;
use tdt\core\spectql\implementation\interpreter\executers\base\AbstractUniversalFilterNodeExecuter;
use tdt\core\spectql\implementation\interpreter\IInterpreterControl;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

class LimitFilterExecuter extends AbstractUniversalFilterNodeExecuter
{

    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn)
    {

        $this->filter = $filter;

        //get source environment header
        $executer = $interpreter->findExecuterFor($filter->getSource());
        $this->executer = $executer;

        $this->executer->initExpression($filter->getSource(), $topenv, $interpreter, $preferColumn);

        $this->header = $this->executer->getExpressionHeader();
    }

    public function getExpressionHeader()
    {
        return $this->header;
    }

    public function evaluateAsExpression()
    {

        $sourceheader = $this->executer->getExpressionHeader();
        $sourcecontent = $this->executer->evaluateAsExpression();

        //create a new empty output table

        $newRows = new UniversalFilterTableContent();

        $offset = $this->filter->getOffset();
        $limit = $this->filter->getLimit();

        for ($index = $offset; $index < $offset + $limit; $index++) {
            try {
                $newRow = $sourcecontent->getRow($index);
                $newRows->addRow($newRow);
            } catch (Exception $ex) {

            }
        }

        $sourcecontent->tryDestroyTable();

        return $newRows;
    }

    public function cleanUp()
    {
        try {
            $this->executer->cleanUp();
        } catch (Exception $ex) {

        }
    }

    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex)
    {
        $arr = $this->executer->filterSingleSourceUsages($this->filter, 0);

        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }
}

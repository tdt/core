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

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\interpreter\Environment;
use Tdt\Core\Spectql\implementation\interpreter\executers\base\AbstractUniversalFilterNodeExecuter;
use Tdt\Core\Spectql\implementation\interpreter\IInterpreterControl;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

class TableAliasExecuter extends AbstractUniversalFilterNodeExecuter
{

    private $executer;
    private $header;

    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn)
    {
        $this->filter = $filter;

        $this->executer = $interpreter->findExecuterFor($filter->getSource());
        $this->executer->initExpression($filter->getSource(), $topenv, $interpreter, $preferColumn);

        $this->header = $this->executer->getExpressionHeader()->cloneHeader();
        $this->header->renameAlias($filter->getAlias());
    }

    public function getExpressionHeader()
    {
        return $this->header;
    }

    public function evaluateAsExpression()
    {
        return $this->executer->evaluateAsExpression();
    }

    public function cleanUp()
    {
        try {
            $this->executer->cleanUp();
        } catch (Exception $ex) {

        }
    }

    public function modififyFiltersWithHeaderInformation()
    {
        parent::modififyFiltersWithHeaderInformation();
        $this->executer->modififyFiltersWithHeaderInformation();
    }

    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex)
    {
        $arr = $this->executer->filterSingleSourceUsages($this->filter, 0);

        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }
}

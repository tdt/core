<?php

/**
 * Executes the LimitFilter filter
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\data\UniversalFilterTableContent;
use tdt\core\universalfilter\interpreter\Environment;
use tdt\core\universalfilter\interpreter\executers\base\AbstractUniversalFilterNodeExecuter;
use tdt\core\universalfilter\interpreter\IInterpreterControl;
use tdt\core\universalfilter\UniversalFilterNode;
use tdt\exceptions\TDTException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use tdt\core\utility\Config;

class LimitFilterExecuter extends AbstractUniversalFilterNodeExecuter {

    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn) {
        $this->filter = $filter;

        //get source environment header
        $executer = $interpreter->findExecuterFor($filter->getSource());
        $this->executer = $executer;

        $this->executer->initExpression($filter->getSource(), $topenv, $interpreter, $preferColumn);

        $this->header = $this->executer->getExpressionHeader();
    }

    public function getExpressionHeader() {
        return $this->header;
    }

    public function evaluateAsExpression() {

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
            } catch (TDTException $ex) {
                // this exception will occur when we go out of bounds in our source
                // do nothing, the row just doesn't get added, and ofcourse subsequent rows won't be added as well 
                // for they do not exist.
                $log = new Logger('SPECTQL');
                $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ALERT));
                $log->addInfo("A request for a row has been made in TableContent, but no more rows exist beyond $index.");
                break;
            }
        }

        $sourcecontent->tryDestroyTable();

        return $newRows;
    }

    public function cleanUp() {
        try {
            $this->executer->cleanUp();
        } catch (Exception $ex) {
            
        }
    }

    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex) {
        $arr = $this->executer->filterSingleSourceUsages($this->filter, 0);

        return $this->combineSourceUsages($arr, $this->filter, $parentNode, $parentIndex);
    }

}

?>

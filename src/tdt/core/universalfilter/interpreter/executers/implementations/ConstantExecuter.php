<?php

/**
 * "Executes" a constant and returns a table
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\data\UniversalFilterTableContent;
use tdt\core\universalfilter\data\UniversalFilterTableContentRow;
use tdt\core\universalfilter\data\UniversalFilterTableHeader;
use tdt\core\universalfilter\data\UniversalFilterTableHeaderColumnInfo;
use tdt\core\universalfilter\interpreter\Environment;
use tdt\core\universalfilter\interpreter\executers\base\AbstractUniversalFilterNodeExecuter;
use tdt\core\universalfilter\interpreter\IInterpreterControl;
use tdt\core\universalfilter\UniversalFilterNode;

class ConstantExecuter extends AbstractUniversalFilterNodeExecuter {

    private $header;
    private $const;
    private $nameOfField;

    private function getFieldName($const) {
        if ($const != "") {
            return "$const";
        } else {
            return "empty";
        }
    }

    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn) {
        $this->filter = $filter;

        $this->const = $filter->getConstant();
        $this->nameOfField = $this->getFieldName($this->const);

        //column
        $cominedHeaderColumn = new UniversalFilterTableHeaderColumnInfo(array($this->nameOfField));

        //new Header
        $this->header = new UniversalFilterTableHeader(array($cominedHeaderColumn), true, true);
    }

    public function getExpressionHeader() {
        return $this->header;
    }

    public function evaluateAsExpression() {
        $id = $this->header->getColumnId();

        $row = new UniversalFilterTableContentRow();
        $row->defineValue($id, $this->const);

        $content = new UniversalFilterTableContent();
        $content->addRow($row);

        return $content;
    }

    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex) {
        return array();
    }

}

?>

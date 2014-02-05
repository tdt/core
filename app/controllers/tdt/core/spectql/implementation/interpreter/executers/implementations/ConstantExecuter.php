<?php

/**
 * "Executes" a constant and returns a table
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

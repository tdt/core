<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\data\UniversalFilterTableContent;
use tdt\core\universalfilter\interpreter\executers\implementations\AggregatorFunctionExecuter;

class LastAggregatorExecuter extends AggregatorFunctionExecuter {

    public function calculateValue(UniversalFilterTableContent $column, $columnId) {
        $data = $this->convertColumnToArray($column, $columnId);
        return $data[count($data) - 1];
    }

    public function errorIfNoItems() {
        return true;
    }

}



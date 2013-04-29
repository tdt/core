<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\data\UniversalFilterTableContent;
use tdt\core\universalfilter\interpreter\executers\implementations\AggregatorFunctionExecuter;

class SumAggregatorExecuter extends AggregatorFunctionExecuter {

    public function calculateValue(UniversalFilterTableContent $column, $columnId) {
        $data = $this->convertColumnToArray($column, $columnId);
        return array_sum($data);
    }

    public function keepFullInfo() {
        return false;
    }

    public function getName($name) {
        return "sum_" . $name;
    }

    public function errorIfNoItems() {
        return false;
    }

}

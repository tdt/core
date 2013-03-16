<?php

namespace tdt\core\universalfilter\interpreter\executers\implementations;

use tdt\core\universalfilter\data\UniversalFilterTableContent;
use tdt\core\universalfilter\interpreter\executers\implementations\AggregatorFunctionExecuter;

/* max */
class MaxAggregatorExecuter extends AggregatorFunctionExecuter {

    public function calculateValue(UniversalFilterTableContent $column, $columnId) {
        $data = $this->convertColumnToArray($column, $columnId);
        return max($data);
    }

    public function keepFullInfo() {
        return false;
    }

    public function getName($name) {
        return "max_" . $name;
    }

    public function errorIfNoItems() {
        return true;
    }

}



<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\data\UniversalFilterTableContent;
use tdt\core\spectql\implementation\interpreter\executers\implementations\AggregatorFunctionExecuter;

class FirstAggregatorExecuter extends AggregatorFunctionExecuter {

    public function calculateValue(UniversalFilterTableContent $column, $columnId) {

        $data = $this->convertColumnToArray($column, $columnId);
        return array_shift($data);
    }

    public function errorIfNoItems() {
        return true;
    }

    public function keepFullInfo() {
        return false;
    }

    public function getName($name) {
        return "first_" . $name;
    }

}

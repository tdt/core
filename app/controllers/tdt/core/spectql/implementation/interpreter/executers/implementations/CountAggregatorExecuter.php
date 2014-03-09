<?php

namespace tdt\core\spectql\implementation\interpreter\executers\implementations;

use tdt\core\spectql\implementation\data\UniversalFilterTableContent;
use tdt\core\spectql\implementation\interpreter\executers\implementations\AggregatorFunctionExecuter;

class CountAggregatorExecuter extends AggregatorFunctionExecuter
{

    public function getName($name) {
        return "count_" . $name;
    }

    public function calculateValue(UniversalFilterTableContent $content, $columnId) {
        return $content->getRowCount();
    }

    public function keepFullInfo() {
        return false;
    }

    public function combinesMultipleColumns() {
        return true;
    }

}


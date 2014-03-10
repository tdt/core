<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\data\UniversalFilterTableContent;
use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\AggregatorFunctionExecuter;

class MinAggregatorExecuter extends AggregatorFunctionExecuter
{

    public function calculateValue(UniversalFilterTableContent $column, $columnId)
    {
        $data = $this->convertColumnToArray($column, $columnId);
        return min($data);
    }

    public function keepFullInfo()
    {
        return false;
    }

    public function getName($name)
    {
        return "min_" . $name;
    }

    public function errorIfNoItems()
    {
        return true;
    }
}

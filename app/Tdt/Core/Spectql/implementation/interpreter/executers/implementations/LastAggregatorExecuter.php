<?php

namespace Tdt\Core\Spectql\implementation\interpreter\executers\implementations;

use Tdt\Core\Spectql\implementation\data\UniversalFilterTableContent;
use Tdt\Core\Spectql\implementation\interpreter\executers\implementations\AggregatorFunctionExecuter;

class LastAggregatorExecuter extends AggregatorFunctionExecuter
{

    public function calculateValue(UniversalFilterTableContent $column, $columnId)
    {
        $data = $this->convertColumnToArray($column, $columnId);
        return $data[count($data) - 1];
    }

    public function errorIfNoItems()
    {
        return true;
    }

    public function keepFullInfo()
    {
        return false;
    }

    public function getName($name)
    {
        return "last_" . $name;
    }
}

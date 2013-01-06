<?php

/**
 * This file contains all evaluators for aggregators
 * 
 * @todo Aggregators should NOT crash on null-values. But I have no idea how the internal php-methods handle that...
 * @todo We should rewrite the Aggregators to be able to use them on very large datasets... (don't use internal php-methods)
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
/* average */
class AverageAggregatorExecuter extends AggregatorFunctionExecuter {

    public function calculateValue(UniversalFilterTableContent $column, $columnId) {
        $data = $this->convertColumnToArray($column, $columnId);
        $sum = array_sum($data);
        $count = count($data);
        if ($count == 0) {
            return 0;
        }
        return $sum / $count;
    }

    public function keepFullInfo() {
        return false;
    }

    public function getName($name) {
        return "avg_" . $name;
    }

    public function errorIfNoItems() {
        return false;
    }

}

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

/* min */

class MinAggregatorExecuter extends AggregatorFunctionExecuter {

    public function calculateValue(UniversalFilterTableContent $column, $columnId) {
        $data = $this->convertColumnToArray($column, $columnId);
        return min($data);
    }

    public function keepFullInfo() {
        return false;
    }

    public function getName($name) {
        return "min_" . $name;
    }

    public function errorIfNoItems() {
        return true;
    }

}

/* sum */

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

/* first */

class FirstAggregatorExecuter extends AggregatorFunctionExecuter {

    public function calculateValue(UniversalFilterTableContent $column, $columnId) {
        $data = $this->convertColumnToArray($column, $columnId);
        return $data[0];
    }

    public function errorIfNoItems() {
        return true;
    }

}

/* last */

class LastAggregatorExecuter extends AggregatorFunctionExecuter {

    public function calculateValue(UniversalFilterTableContent $column, $columnId) {
        $data = $this->convertColumnToArray($column, $columnId);
        return $data[count($data) - 1];
    }

    public function errorIfNoItems() {
        return true;
    }

}

/* count */

class CountAggregatorExecuter extends AggregatorFunctionExecuter {

    public function getName($name) {
        return "count_" . $name;
    }

    public function calculateValue(UniversalFilterTableContent $column, $columnId) {
        return $column->getRowCount();
    }

    public function keepFullInfo() {
        return false;
    }

    public function combinesMultipleColumns() {
        return true;
    }

}

?>

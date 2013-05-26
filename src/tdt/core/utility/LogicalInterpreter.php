<?php

/**
 *
 * This class process logical statements in the form of:
 *     array( $subject, $operator, $value)
 *     where subject in itself can containt another array with a statement i.e.
 *     array( array ( x, =, y ), "AND", array( x, <, y))
 *     It then compares the x and y value, the x value is passed along with the values hash-array.
 */

namespace tdt\core\utility;

class LogicalInterpreter{

    public function __construct(){
    }

    public function interpret($clause,$values){

        if($this->isSingleStatement($clause)){
            return $this->processStatement($clause,$values);
        }else if($this->isOrStatement($clause)){
            return $this->interpret($clause[0],$values) || $this->interpret($clause[2],$values);
        }else if($this->isAndStatement($clause)){
            return $this->interpret($clause[0],$values) && $this->interpret($clause[2],$values);
        }
    }

    /**
     * Process a statement, return true or false based on the
     * parameters & values given.
     * $clause: array( value: from data, operator, value: to compare with)
     */
    private function processStatement($clause, $values){

        $value = "";
        if(empty($values[$clause[0]])){
            // TODO: Write this to the logs.
            return false;
        }
        $value = $values[$clause[0]];

        switch($clause[1]){
            case "=":
                return $value == $clause[2];
                break;
            case "!=":
                return $value != $clause[2];
                break;
            case "<":
                return $value < $clause[2];
                break;
            case ">":
                return $value > $clause[2];
                break;
            case "<=":
                return $value <= $clause[2];
                break;
            case ">=":
                return $value >= $clause[2];
                break;
            case "LIKE":
                return preg_match($clause[2],$value);
                break;
        }
    }

    /**
     * This function returns true if the clause array
     * it receives is a single binary statement, false
     * if it isn't.
     */
    private function isSingleStatement($clause){
        if(!empty($clause[0]) && !is_array($clause[0])){
            return true;
        }

        return false;
    }

    /**
     * The function returns true if the statement is an OR statement.
     */
    private function isOrStatement($clause){
        if(!empty($clause[1]) && $clause[1] == "OR"){
            return true;
        }

        return false;
    }

    /**
     * The function returns true if the statement is an OR statement.
     */
    private function isAndStatement($clause){
        if(!empty($clause[1]) && $clause[1] == "AND"){
            return true;
        }

        return false;
    }
}
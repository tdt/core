<?php

/**
 * Executes the Check In List filter
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class CheckInFunctionExecuter extends UnaryFunctionExecuter {
    
    private function constantsToString(){
        $arr = $this->filter->getConstants();
        $outarr = array();
        foreach ($arr as $constant) {
            array_push($outarr, $constant->getConstant());
        }
        
        return implode("-", $outarr);
    }
    
    
    public function getName($name){
        return "_".$name."_in_".$this->constantsToString()."_";
    }
    
    public function doUnaryFunction($value){
        $arr = $this->filter->getConstants();
        foreach ($arr as $constant) {
            if($constant->getConstant()==$value) {return "true";}
        }
        return "false";
    }
}

?>

<?php
/**
 * Interface of all executers
 *
 * @package The-Datatank/universalfilter/interpreter/executers/base
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 * 
 * @see universalfilter/interpreter/executers/base/IUniversalFilterNodeExecuter.interface.php for the documentation of these methods
 */
abstract class AbstractUniversalFilterNodeExecuter {
    
    protected $filter;//Need to be set in initExpression
    
    public abstract function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn);

    public abstract function getExpressionHeader();
    
    public abstract function evaluateAsExpression();

    public function cleanUp(){}
    
    
    public function getTableNames(){
        $expectedheadernames=array();
        $header = $this->getExpressionHeader();
        for ($index = 0; $index < $header->getColumnCount(); $index++) {
            $columnId = $header->getColumnIdByIndex($index);
            $columnInformation = $header->getColumnInformationById($columnId);
            array_push($expectedheadernames, $columnInformation->getFullName("_"));
        }
        return $expectedheadernames;
    }
    
    public function modififyFiltersWithHeaderInformation(){
        $filter = $this->filter;
        $expectedheadernames = $this->getTableNames();
        
        //sets the data on the filter
        $filter->attach(ExpectedHeaderNamesAttachment::$ATTACHMENTID, new ExpectedHeaderNamesAttachment($expectedheadernames));
    }
    
    
    /**
     * We want to give back the biggest subtree which only uses one source.
     * So, we need a method to combine children...
     * 
     * Method:
     * if all your dependencies contain one source -> return new SourceUsageData with you in
     * if some of your dependencies contain more than one source, 
     *    or there are two different dependencies with a different source 
     *      -> join all and return.
     * 
     * @param array $arr The SourceUsageData-array to combine
     */
    protected function combineSourceUsages(array /* of SourceUsageData */ $arr, $filter, $parent, $parentIndex){
        $foundsources=array();
        foreach($arr as $sourceUsage){
            $id = $sourceUsage->getSourceId();
            if(!in_array($id, $foundsources)){
                array_push($foundsources, $id);
            }
        }
        if(count($foundsources)==1){
            //only one source used
            return array(new SourceUsageData($filter, $parent, $parentIndex, $foundsources[0]));
        }else{
            return $arr;
        }
    }
    
    public abstract function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex);
    
    
}
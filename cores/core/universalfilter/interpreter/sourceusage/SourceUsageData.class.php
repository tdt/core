<?php

/**
 * Contains information about sources that are used by a (part of) a query.
 * 
 * All filters below (and inclusive) $filterSourceNode only use ONE source, namely $sourceId.
 * And this is the biggest subtree.
 * So, $filterParentNode combines multiple sources.
 * This is $filterParentSourceIndex of the $filterParentNode.
 *
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class SourceUsageData {
    private $filterSourceNode;
    private $filterParentNode;
    private $filterParentSourceIndex;
    private $sourceId;
    
    /**
     * Constructs a new instance of this class
     * 
     * @param UniversalFilterNode $filterSourceNode
     * @param UniversalFilterNode $filterParentNode
     * @param int $filterParentSourceIndex
     * @param string $sourceId 
     */
    public function __construct(UniversalFilterNode $filterSourceNode, UniversalFilterNode $filterParentNode, $filterParentSourceIndex, $sourceId) {
        $this->filterSourceNode=$filterSourceNode;
        $this->filterParentNode=$filterParentNode;
        $this->filterParentSourceIndex=$filterParentSourceIndex;
        $this->sourceId=$sourceId;
    }

    /**
     *
     * @return type 
     */
    public function getFilterSourceNode(){
        return $this->filterSourceNode;
    }
    
    public function getFilterParentNode(){
        return $this->filterParentNode;
    }
    
    public function getFilterParentSourceIndex(){
        return $this->filterParentSourceIndex;
    }
    
    public function getSourceId(){
        return $this->sourceId;
    }
    
    public function setFilterSourceNode($filterSourceNode){
        $this->filterSourceNode = $filterSourceNode;
    }
    
    public function setFilterParentNode($filterParentNode){
        $this->filterParentNode = $filterParentNode;
    }
    
    public function setFilterParentSourceIndex($filterParentSourceIndex){
        $this->filterParentSourceIndex = $filterParentSourceIndex;
    }
    
    public function setSourceId($sourceId){
        $this->sourceId=$sourceId;
    }
}

?>

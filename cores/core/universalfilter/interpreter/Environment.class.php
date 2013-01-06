<?php

/**
 * An environment is passed to the filterexecuters while executing a query
 *
 * @package The-Datatank/universalfilter/interpreter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class Environment {
    
    /**
     * Single values
     */
    private $singlevaluecolumns = array();
    private $singlevaluerows = array();
    
    /**
     * Adds a single environment value
     * @param UniversalFilterTableHeaderColumnInfo $column
     * @param UniversalFilterTableContentRow $datarow 
     */
    public function addSingleValue(UniversalFilterTableHeaderColumnInfo $column, UniversalFilterTableContentRow $datarow) {
        array_push($this->singlevaluecolumns, $column);
        array_push($this->singlevaluerows, $datarow);
    }
    
    /**
     * Returns a single value -> header
     * @param int $index
     * @return UniversalFilterTableHeaderColumnInfo
     */
    public function getSingleValueHeader($index){
        return $this->singlevaluecolumns[$index];
    }
    
    /**
     * Returns a single value -> row
     * @param int $index
     * @return UniversalFilterTableContentRow 
     */
    public function getSingleValue($index){
        return $this->singlevaluerows[$index];
    }
    
    /**
     * Sets the single value on a certain index
     * @param int $index
     * @param UniversalFilterTableContentRow $datarow 
     */
    public function setSingleValue($index, UniversalFilterTableContentRow $datarow) {
        $this->singlevaluerows[$index] = $datarow;
    }
    
    /**
     * Gets the number of single values
     * @return int
     */
    public function getSingleValueCount(){
        return count($this->singlevaluecolumns);
    }

    /**
     * Manage tables
     */
    
    private $table=null;
    
    /**
     * set the current table
     */
    public function setTable(UniversalFilterTable $table) {
        $this->table=$table;
    }
    
    /**
     * get the last added table
     * @return UniversalFilterTable
     */
    public function getTable(){
        return $this->table;
    }

    /**
     * Clone Environment
     */
    public function newModifiableEnvironment(){
        $newEnv=new Environment();
        $newEnv->setTable($this->getTable());
        $newEnv->singlevaluerows=$this->singlevaluerows;
        $newEnv->singlevaluecolumns=$this->singlevaluecolumns;
        return $newEnv;
    }
}

?>

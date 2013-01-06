<?php

/**
 * A column in the header of the universal representation of a table
 *
 * @package The-Datatank/universalfilter/data
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class UniversalFilterTableHeaderColumnInfo {
    private $completeColumnNameParts; //array(package, package, resource, subtable, ...)
    private $columnId; //unique Id
    
    private $isGrouped;
    
    private $isLinked;
    private $linkedTable;
    private $linkedTableKey;
    
    public function __construct(array $completeColumnName, $isLinked=false, $linkedTable=null, $linkedTableKey=null, $isGrouped=false) {
        $this->completeColumnNameParts = $completeColumnName;
        $this->isLinked = $isLinked;
        $this->linkedTable = $linkedTable;
        $this->linkedTableKey = $linkedTableKey;
        $this->columnId = uniqid();
        $this->isGrouped = $isGrouped;
    }
    
    /**
     * Get the unique id of this column
     * @return string 
     */
    public function getId(){
        return $this->columnId;
    }
    
    /**
     * Gets the last part of the name of this column
     * @return string 
     */
    public function getName(){
        return $this->completeColumnNameParts[count($this->completeColumnNameParts)-1];//last
    }
    
    public function getFullName($separator="."){
        return implode($separator, $this->completeColumnNameParts);
    }
    
    /**
     * returns true if this column is grouped
     * @return bool 
     */
    public function isGrouped(){
        return $this->isGrouped;
    }
    
    /**
     * renames this column
     * @param type $newColumName 
     */
    public function aliasColumn($newColumName){
        if(strpos($newColumName, ".")!=-1){
            $oldName = array_pop($this->completeColumnNameParts);
            $this->completeColumnNameParts[] = $newColumName;
        }else{
            throw new Exception("\"$newColumName\" is an illegal alias.");
        }
    }
    
    /**
     * checks if the given name matches this column
     * @param array $nameParts
     * @return bool 
     */
    public function matchName(array $nameParts){
        return UniversalFilterTableHeaderColumnInfo::algorithmMatchName($this->completeColumnNameParts, $nameParts);
    }
    
    /**
     * The algorithm used to match names...
     * 
     * @param array $fullNameParts
     * @param array $nameParts
     * @return boolean Do we have a match?
     */
    public static function algorithmMatchName(array $fullNameParts, array $nameParts){
        $completeCount = count($fullNameParts);
        $partCount = count($nameParts);
        if($partCount>$completeCount){
            return false;
        }
        for ($index = 0; $index < $partCount; $index++) {
            $completePart = $fullNameParts[$completeCount-1-$index];
            $partialPart = $nameParts[$partCount-1-$index];
            if($completePart!=$partialPart){
                return false;
            }
        }
        return true;
    }


    
    /**
     * clone this columnInfo
     * @return UniversalFilterTableHeaderColumnInfo 
     */
    public function cloneColumnInfo(){
        $a = new UniversalFilterTableHeaderColumnInfo($this->completeColumnNameParts);
        $a->isLinked=$this->isLinked;
        $a->isGrouped=$this->isGrouped;
        $a->linkedTable=$this->linkedTable;
        $a->linkedTableKey=$this->linkedTableKey;
        $a->columnId=$this->columnId;
        return $a;
    }
    
    /**
     * clones this column, but makes it distinctive by id
     * @return UniversalFilterTableHeaderColumnInfo
     */
    public function cloneColumnNewId(){
        $a = $this->cloneColumnInfo();
        $a->columnId = uniqid();
        return $a;
    }
    
    /**
     * Make a new Column with the give id.
     * (the "is based upon" info is not used...)
     * @param type $newFieldName
     * @return UniversalFilterTableHeaderColumnInfo 
     */
    public function cloneBaseUpon($newFieldName){
        $a = new UniversalFilterTableHeaderColumnInfo(array($newFieldName));
        return $a;
    }
    
    /**
     * Clones this column, but sets it to be grouped
     * @return UniversalFilterTableHeaderColumnInfo
     */
    public function cloneColumnGrouped(){
        $a = $this->cloneColumnInfo();
        $a->columnId = uniqid();
        $a->isGrouped=true;
        return $a;
    }
    
    /**
     * Clones this column, with a new tablename
     */
    public function cloneColumnTableAlias($tablename){
        $newColum = $this->cloneColumnInfo();
        $newColum->completeColumnNameParts=array($tablename, $this->completeColumnNameParts[count($this->completeColumnNameParts)-1]);
        return $newColum;
    }
    
    /**
     * Clones this column with the specified id
     */
    public function cloneColumnWithId($newColumnId){
        $newColum = $this->cloneColumnInfo();
        $newColum->columnId = $newColumnId;
        return $newColum;
    }
    
}

?>

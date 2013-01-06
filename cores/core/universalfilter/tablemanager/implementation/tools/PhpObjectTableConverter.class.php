<?php

/**
 * This class can convert a php-object to a table (as used by the interpreter)
 *
 * @package The-Datatank/universalfilter/tablemanager/tools
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class PhpObjectTableConverter {
    
    public static $ID_FIELD="_id";
    public static $ID_KEY="_key_";
    
    function is_assoc(array $array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

    
    /**
     * Finds all paths from $root by following the fields with names in $path
     * (Splits on arrays)
     * 
     * @param type $root
     * @param type $path 
     */
    private function findTablePhpArray($root, $path, $parentitemindex){
        if(count($path)==1){
            $parentitemindex++;
        }
        
        if(!empty($path)){
            $oldpath=$path;
            $fieldToSearch = array_shift($path);
            
            if(is_array($root) || is_object($root)){
                $fieldvalue = null;
                if(is_array($root)){
                    
                    if(true /* SEE NOTE*/ || $this->is_assoc($root)){
                        $fieldvalue = $root[$fieldToSearch];
                        
                        return $this->findTablePhpArray($fieldvalue, $path, $parentitemindex);
                    }else{// numeric array or empty array -> search in children...
                        /* NOTE: */
                        /* if we would implement the _id and _key fields, this code would be better... */
                        /* but as they are not implemented, the user(!) will have problems finding the correct row... */
                        $newfieldvalue = array();
                        
                        for ($i = 0; $i < count($root); $i++) {
                            $copyoldpath = $oldpath;
                            $temparr = $this->findTablePhpArray($root[$i], $copyoldpath, 0/*todo*/);
                            $newfieldvalue = array_merge($newfieldvalue, $temparr);
                        }
                        $fieldvalue = $newfieldvalue;
                        
                        return $fieldvalue;
                    }
                }else{
                    if(isset ($root->$fieldToSearch)){
                        $fieldvalue = $root->$fieldToSearch;
                        
                        return $this->findTablePhpArray($fieldvalue, $path, $parentitemindex);
                    }else{
                        return array();
                    }
                }
            }else{
                return array();
            }
        }else{
            if(is_object($root)){
                //return array(array("object" => $root, "parentindex" => $parentitemindex));
                
                //NOTE: we do save the indices... it's a object.......
                $rootarr = array();
                foreach($root as $i => $ritem){
                    $obj = new stdClass();
                    $obj->index=$i;
                    $obj->value=$ritem;
                    array_push($rootarr, array("object" => $obj, "parentindex" => $parentitemindex));
                }
                return $rootarr;
            }else if(is_array($root)){
                //NOTE: we don't save the indices... it's an array.......
                // (unless we find nonNumericIndices)
                $foundNonNumericIndices = $this->is_assoc($root);
                
                $rootarr = array();
                foreach($root as $i => $ritem){
                    if(is_object($ritem)){//this row is a object = ok
                        //ok!
                    }else if(is_array ($ritem)){//this row is an array = need conversion
                        $obj = new stdClass();
                        foreach($ritem as $a => $aitem){
                            $obj = new stdClass();
                            $index="index_".$a;
                            $obj->$index=$aitem;
                            array_push($rootarr, array("object" => $obj, "parentindex" => $parentitemindex));
                        }
                        $ritem=$obj;
                    }else{//this row is a value...
                        $obj = new stdClass();
                        $obj->value=$ritem;
                        $ritem = $obj;
                    }
                    
                    if($foundNonNumericIndices){
                        $ritem->index = $i;
                    }
                    
                    array_push($rootarr, array("object" => $ritem, "parentindex" => $parentitemindex));
                }
                return $rootarr;
            }else{
                //should be in the parent table, as a field
                return array();
            }
        }
    }
    
    private function getPhpObjectsByIdentifier($splitedId,$resource){
        $phpObj = $this->findTablePhpArray($resource, isset($splitedId[3])?$splitedId[3] : null, -1);
        
        return $phpObj;
    }
    
    
    private function parseColumnName($name){
        return preg_replace("/[^A-Za-z0-9]/", "_", $name);
    }
    
    
    private function getPhpObjectTableHeader($nameOfTable, $objects){
        $columns = array();
        $columnNames = array();

        foreach($objects as $index => $data){
            $parentindex = $data["parentindex"];
            $obj = $data["object"];
            
            $arr_obj = get_object_vars($obj);
            foreach($arr_obj as $key => $value){
                $columnName=$this->parseColumnName($key);
                
                if(!in_array($columnName, $columnNames)){
                    //new field: add header
                    array_push($columnNames, $columnName);
                    $isLinked=false;
                    $linkedTable=null;
                    $linkedTableKey=null;

                    /* TODO: generate linked data info */
                    /*if(is_array($value) || is_object($value)){
                        //new field is subtable
                        $isLinked=true;
                        $linkedTable=$totalId.".".$columnName;//TODO: totalId not defined !!!
                        $linkedTableKey=PhpObjectTableConverter::$ID_KEY.$columnName;//todo: check first if field does not exists...
                    }*/
                    
                    array_push($columns, new UniversalFilterTableHeaderColumnInfo(array($columnName), $isLinked, $linkedTable, $linkedTableKey));
                }
            }
        }
        
        // add id field (just a field...)
        //array_push($columns, new UniversalFilterTableHeaderColumnInfo(array(PhpObjectTableConverter::$ID_FIELD), false, null, null)); //

        // add key_parent field
        //array_push($columns, new UniversalFilterTableHeaderColumnInfo(array(PhpObjectTableConverter::$ID_KEY.$nameOfTable), false, null, null));
        
        $header = new UniversalFilterTableHeader($columns, false, false);
        
        return $header;
    }
    
    public function getPhpObjectTableContent($header, $nameOfTable, $objects){
        $rows=new UniversalFilterTableContent();
        
        $subObjectIndex = array();
        
        //optimalisation: build name->id map
        $idMap=array();
        for($index=0;$index<$header->getColumnCount();$index++){
            $columnId = $header->getColumnIdByIndex($index);
            $columnName = $header->getColumnNameById($columnId);
            
            $idMap[$columnName] = $columnId;
        }
        
        foreach($objects as $index => $data){
            $parentindex = $data["parentindex"];
            $obj = $data["object"];

            $arr_obj = get_object_vars($obj);
            $currentrow=new UniversalFilterTableContentRow();
            $found=array();
            
            
            foreach($arr_obj as $key => $value){
                $columnName = $this->parseColumnName($key);
                $columnId = $idMap[$columnName];//$header->getColumnIdByName($columnName);
                
                if(is_array($value) || is_object($value)){
                    //we have a subobject
                    //what's it index?
                    $subObjIndex=0;
                    if(isset($subObjectIndex[$columnName])){
                        $subObjectIndex[$columnName]++;
                        $subObjIndex=$subObjectIndex[$columnName];
                    }else{
                        $subObjectIndex[$columnName]=0;
                    }
                    
                    $id = "id_".$subObjIndex;
                    //FOR NOW: just display "object" (TODO)  As the id and key field do not exist anymore...
                    $id = "<<object>>";
                    
                    $currentrow->defineValueId($columnId, $id);
                }else{
                    $currentrow->defineValue($columnId, $value);//what if we have a combination of the two?
                }
                array_push($found, $columnId);
            }
            
            for ($i = 0; $i < $header->getColumnCount(); $i++) {
                $columnId = $header->getColumnIdByIndex($i);
                if(!in_array($columnId, $found)){//we did not defined this value yet...
                    $currentrow->defineValue($columnId, null);
                }
                
            }
            
            //add value id field
            //$columnId = $idMap[PhpObjectTableConverter::$ID_FIELD];//$header->getColumnIdByName(PhpObjectTableConverter::$ID_FIELD);
            //$currentrow->defineValue($columnId, $parentindex);
            //array_push($found, $columnId);
            
            //add value key_parent field
            //$columnId = $idMap[PhpObjectTableConverter::$ID_KEY.$nameOfTable];//$header->getColumnIdByName(PhpObjectTableConverter::$ID_KEY.$nameOfTable);
            //$currentrow->defineValue($columnId, $index);
            //array_push($found, $columnId);
            
            $rows->addRow($currentrow);
        }
        
        return $rows;
    }
    
    public function getPhpObjectTable($splitedId,$objects){
        $objects = $this->getPhpObjectsByIdentifier($splitedId,$objects);
        
        $nameOfTable=$splitedId[1];
        if(isset($splitedId[3]) && count($splitedId[3])>0){
            $nameOfTable=$splitedId[3][count($splitedId[3])-1];
        }
        
        $header = $this->getPhpObjectTableHeader($nameOfTable, $objects);
        
        //var_dump($header);
        
        $body = $this->getPhpObjectTableContent($header, $nameOfTable, $objects);
        
        //echo "<br><br>";
        //var_dump($body);
        
        return new UniversalFilterTable($header, $body);
    }

    public function getPhpObjectTableWithHeader($splitedId,$objects,$header){
        $objects = $this->getPhpObjectsByIdentifier($splitedId,$objects);
        
        $nameOfTable=$splitedId[1];
        if(count($splitedId[2])>0){
            $nameOfTable=$splitedId[2][count($splitedId[2])-1];
        }
        
        $body = $this->getPhpObjectTableContent($header, $nameOfTable, $objects);
        
        return new UniversalFilterTable($header, $body);
    }

    public function getNameOfTable($splitedId){

        $nameOfTable=$splitedId[1];
        if(count($splitedId[2])>0){
            $nameOfTable=$splitedId[2][count($splitedId[2])-1];
        }
        return $nameOfTable;
    }

}

?>

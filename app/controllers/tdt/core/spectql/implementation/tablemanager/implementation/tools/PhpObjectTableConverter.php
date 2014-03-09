<?php

/**
 * This class can convert a php-object to a table (as used by the interpreter)
 *
 * @package The-Datatank/universalfilter/tablemanager/tools
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\spectql\implementation\tablemanager\implementation\tools;

use tdt\core\spectql\implementation\data\UniversalFilterTable;
use tdt\core\spectql\implementation\data\UniversalFilterTableContent;
use tdt\core\spectql\implementation\data\UniversalFilterTableContentRow;
use tdt\core\spectql\implementation\data\UniversalFilterTableHeader;
use tdt\core\spectql\implementation\data\UniversalFilterTableHeaderColumnInfo;

class PhpObjectTableConverter
{

    public static $ID_FIELD = "_id";
    public static $ID_KEY = "_key_";

    function is_assoc(array $array) {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * Finds all paths from $root by following the fields with names in $path
     * (Splits on arrays)
     *
     * @param type $root
     * @param type $path
     */
    private function findTablePhpArray($root, $path, $parentitemindex)
    {

        if (count($path) == 1) {
            $parentitemindex++;
        }

        if (!empty($path)) {

            $oldpath = $path;
            $fieldToSearch = array_shift($path);

            if (is_array($root) || is_object($root)) {

                $fieldvalue = null;

                if (is_array($root)) {

                    if (true /* SEE NOTE */ || $this->is_assoc($root)) {
                        $fieldvalue = $root[$fieldToSearch];

                        return $this->findTablePhpArray($fieldvalue, $path, $parentitemindex);
                    } else {// numeric array or empty array -> search in children...
                        /* NOTE: */
                        /* if we would implement the _id and _key fields, this code would be better... */
                        /* but as they are not implemented, the user(!) will have problems finding the correct row... */
                        $newfieldvalue = array();

                        for ($i = 0; $i < count($root); $i++) {
                            $copyoldpath = $oldpath;
                            $temparr = $this->findTablePhpArray($root[$i], $copyoldpath, 0/* todo */);
                            $newfieldvalue = array_merge($newfieldvalue, $temparr);
                        }
                        $fieldvalue = $newfieldvalue;

                        return $fieldvalue;
                    }
                } else {
                    if (isset($root->$fieldToSearch)) {
                        $fieldvalue = $root->$fieldToSearch;

                        return $this->findTablePhpArray($fieldvalue, $path, $parentitemindex);
                    } else {
                        return array();
                    }
                }
            } else {
                return array();
            }
        } else {

            if (is_object($root)) {

                $obj_arr = array();

                foreach ($root as $key => $property) {

                    $obj = new \stdClass();
                    $obj->index = $key;
                    $obj->value = $property;

                    array_push($obj_arr, array("object" => $obj, "parentindex" => $parentitemindex));
                }

                return $obj_arr;

            } elseif (is_array($root)) {

                $is_assoc = $this->is_assoc($root);

                $rootarr = array();

                foreach ($root as $i => $property) {

                    if (is_array($property)) {

                        $obj = new \stdClass();

                        foreach ($property as $key => $obj_property) {

                            $obj = new \stdClass();
                            $index = $key;//"index_" . $key;
                            $obj->$index = $obj_property;
                            array_push($rootarr, array("object" => $obj, "parentindex" => $parentitemindex));
                        }
                        $property = $obj;

                    } elseif (!is_object($property)) {

                        $obj = new \stdClass();
                        $obj->value = $property;
                        $property = $obj;
                    }

                    if ($is_assoc) {
                        $property->index = $i;
                    }

                    array_push($rootarr, array("object" => $property, "parentindex" => $parentitemindex));
                }

                return $rootarr;
            } else {

                return array();
            }
        }
    }

    private function getPhpObjectsByIdentifier($splitedId, $resource)
    {

        $phpObj = $this->findTablePhpArray($resource, isset($splitedId[3]) ? $splitedId[3] : null, -1);

        return $phpObj;
    }

    // TODO refactor and remove function
    private function parseColumnName($name)
    {
        return $name;
    }

    private function getPhpObjectTableHeader($nameOfTable, $objects)
    {

        $columns = array();
        $columnNames = array();

        foreach ($objects as $index => $data) {
            $parentindex = $data["parentindex"];
            $obj = $data["object"];

            $arr_obj = get_object_vars($obj);
            foreach ($arr_obj as $key => $value) {
                $columnName = $this->parseColumnName($key);

                if (!in_array($columnName, $columnNames)) {

                    // new field: add header
                    array_push($columnNames, $columnName);

                    $isLinked = false;
                    $linkedTable = null;
                    $linkedTableKey = null;

                    /* TODO: generate linked data info */
                    /* if (is_array($value) || is_object($value)) {
                      // new field is subtable
                      $isLinked=true;
                      $linkedTable=$totalId.".".$columnName;//TODO: totalId not defined !!!
                      $linkedTableKey=PhpObjectTableConverter::$ID_KEY.$columnName;//todo: check first if field does not exists...
                      } */

                    array_push($columns, new UniversalFilterTableHeaderColumnInfo(array($columnName), $isLinked, $linkedTable, $linkedTableKey));
                }
            }
        }

        // add id field (just a field...)
        // array_push($columns, new UniversalFilterTableHeaderColumnInfo(array(PhpObjectTableConverter::$ID_FIELD), false, null, null)); //
        // add key_parent field
        // array_push($columns, new UniversalFilterTableHeaderColumnInfo(array(PhpObjectTableConverter::$ID_KEY.$nameOfTable), false, null, null));

        $header = new UniversalFilterTableHeader($columns, false, false);

        return $header;
    }

    /**
     * Get the table content based on the passed PHP objects
     *
     * @return $rows UniversalFilterTableContent
     */
    public function getPhpObjectTableContent($header, $nameOfTable, $objects)
    {

        $rows = new UniversalFilterTableContent();

        $subObjectIndex = array();

        $idMap = array();

        for ($index = 0; $index < $header->getColumnCount(); $index++) {

            $columnId = $header->getColumnIdByIndex($index);
            $columnName = $header->getColumnNameById($columnId);

            $idMap[$columnName] = $columnId;
        }

        // For every object, create a row in the conversion table
        foreach ($objects as $index => $data) {

            $parentindex = $data["parentindex"];
            $obj = $data["object"];

            $arr_obj = get_object_vars($obj);
            $currentrow = new UniversalFilterTableContentRow();

            $found = array();

            foreach ($arr_obj as $key => $value) {

                $columnName = $this->parseColumnName($key);

                if (isset($idMap[$columnName])) {

                    $columnId = $idMap[$columnName];

                    if (is_array($value) || is_object($value)) {

                        // Retrieve the index of the subobject
                        $subObjIndex = 0;

                        if (isset($subObjectIndex[$columnName])) {

                            $subObjectIndex[$columnName]++;
                            $subObjIndex = $subObjectIndex[$columnName];

                        } else {
                            $subObjectIndex[$columnName] = 0;
                        }

                        $id = "id_" . $subObjIndex;

                        // Just display "object" (TODO)  As the id and key field do not exist anymore...
                        $id = "<<object>>";

                        // old: $currentrow->defineValueId($columnId, $id);
                        $currentrow->defineValueId($columnId, $value);
                    } else {
                        $currentrow->defineValue($columnId, $value); //what if we have a combination of the two?
                    }
                    array_push($found, $columnId);
                }
            }

            for ($i = 0; $i < $header->getColumnCount(); $i++) {
                $columnId = $header->getColumnIdByIndex($i);
                if (!in_array($columnId, $found)) {//we did not defined this value yet...
                    $currentrow->defineValue($columnId, null);
                }
            }

            // add value id field
            // $columnId = $idMap[PhpObjectTableConverter::$ID_FIELD];//$header->getColumnIdByName(PhpObjectTableConverter::$ID_FIELD);
            // $currentrow->defineValue($columnId, $parentindex);
            // array_push($found, $columnId);
            // add value key_parent field
            // $columnId = $idMap[PhpObjectTableConverter::$ID_KEY.$nameOfTable];//$header->getColumnIdByName(PhpObjectTableConverter::$ID_KEY.$nameOfTable);
            // $currentrow->defineValue($columnId, $index);
            // array_push($found, $columnId);

            $rows->addRow($currentrow);
        }

        return $rows;
    }

    /**
     * Return a UniversalTable based on the passed PHP objects
     */
    public function getPhpObjectTable($splitedId, $objects)
    {

        $objects = $this->getPhpObjectsByIdentifier($splitedId, $objects);

        $nameOfTable = $splitedId[1];
        if (isset($splitedId[3]) && count($splitedId[3]) > 0) {
            $nameOfTable = $splitedId[3][count($splitedId[3]) - 1];
        }

        $header = $this->getPhpObjectTableHeader($nameOfTable, $objects);
        $body = $this->getPhpObjectTableContent($header, $nameOfTable, $objects);

        return new UniversalFilterTable($header, $body);
    }

    /**
     * Return a UniversalTable based on the passed PHP objects, given a UniversalHeader
     */
    public function getPhpObjectTableWithHeader($splitedId, $objects, $header)
    {

        $objects = $this->getPhpObjectsByIdentifier($splitedId, $objects);

        $nameOfTable = $splitedId[1];
        if (count($splitedId[2]) > 0) {
            $nameOfTable = $splitedId[2][count($splitedId[2]) - 1];
        }

        $body = $this->getPhpObjectTableContent($header, $nameOfTable, $objects);

        return new UniversalFilterTable($header, $body);
    }

    public function getNameOfTable($splitedId)
    {

        $nameOfTable = $splitedId[1];
        if (count($splitedId[2]) > 0) {
            $nameOfTable = $splitedId[2][count($splitedId[2]) - 1];
        }
        return $nameOfTable;
    }
}

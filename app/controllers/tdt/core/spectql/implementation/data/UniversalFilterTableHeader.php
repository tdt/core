<?php

/**
 * The header of the universal representation of a table
 *
 * @package The-Datatank/universalfilter/data
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\spectql\implementation\data;

use tdt\exceptions\TDTException;
use tdt\core\utility\Config;

class UniversalFilterTableHeader {

    private $columns; //array of UniversalFilterTableHeaderColumnInfo
    private $isSingleRowByConstruction;
    private $isSingleColumnByConstruction;

    public function __construct($columns, $isSingleRowByConstruction, $isSingleColumnByConstruction) {
        $this->columns = $columns;
        $this->isSingleRowByConstruction = $isSingleRowByConstruction;
        $this->isSingleColumnByConstruction = $isSingleColumnByConstruction;
    }

    /**
     * Rename this table
     */
    public function renameAlias($newname) {
        $newcolumns = array();
        foreach ($this->columns as $column) {
            array_push($newcolumns, $column->cloneColumnTableAlias($newname));
        }
        $this->columns = $newcolumns;
    }

    /**
     * Gets the columnId for a given name
     * @return string
     */
    public function getColumnIdByName($columnName) {
        $columnNameParts = explode(".", $columnName);
        $found = false;
        $id = null;
        foreach ($this->columns as $column) {
            if ($column->matchName($columnNameParts)) {
                if (!$found) {
                    $found = true;
                    $id = $column->getId();
                } else {
                    $exception_config = array();
                    $exception_config["log_dir"] = Config::get("general", "logging", "path");
                    $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                    throw new TDTException(500, array("The identifier  $columnName is not unique."), $exception_config);
                }
            }
        }
        return $id;
    }

    /**
     * Gets the columnName for a given id
     */
    public function getColumnNameById($id) {
        return $this->getColumnInformationById($id)->getName();
    }

    /**
     * Gets the unique columnName for a given id
     */
    public function getColumnUniqueNameById($id) {
        $info = $this->getColumnInformationById($id);
        $name = $info->getName();
        try {
            $this->getColumnIdByName($name);
        } catch (Exception $e) {
            $name = $info->getFullName();
            try {
                $this->getColumnIdByName($name);
            } catch (Exception $e) {
                //hmmm....
                $name = $name . " (" . $info->getId() . ")";
            }
        }
        return $name;
    }

    /**
     * returns the number of columns
     */
    public function getColumnCount() {
        return count($this->columns);
    }

    /**
     * get a certain column id
     */
    public function getColumnIdByIndex($index) {
        return $this->columns[$index]->getId();
    }

    /**
     * get columnInformation
     * @return UniversalFilterTableHeaderColumnInfo
     */
    public function getColumnInformationById($id) {
        foreach ($this->columns as $column) {
            if ($column->getId() == $id) {
                return $column;
            }
        }
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(500, array("TableHeader - ColumnInformation not found for id: " . $id . ""), $exception_config);
    }

    /**
     * get columnInformation
     * @return UniversalFilterTableHeaderColumnInfo
     */
    public function getColumnInformationByIndex($index) {
        return $this->getColumnInformationById($this->getColumnIdByIndex($index));
    }

    /**
     * returns if this table is constructed that way only one row can exist (e.g. after FIRST() or AVG() )
     */
    public function isSingleRowByConstruction() {
        return $this->isSingleRowByConstruction;
    }

    /**
     * sets the isSingleRowByConstruction value
     */
    public function setIsSingleRowByConstruction($value) {
        $this->isSingleRowByConstruction = $value;
    }

    /**
     * return if this table is constructed that way only one column can exist (e.g. by a columnselector)
     */
    public function isSingleColumnByConstruction() {
        return $this->isSingleColumnByConstruction;
    }

    /**
     * return if this table is constructed that way only one cell can exist
     */
    public function isSingleCellByConstruction() {
        return $this->isSingleColumnByConstruction() && $this->isSingleRowByConstruction();
    }

    /**
     * returns the only columnId (if a column)
     */
    public function getColumnId() {
        if (!$this->isSingleColumnByConstruction()) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("TableHeader - Not a single column."), $exception_config);
        }
        return $this->getColumnIdByIndex(0);
    }

    /**
     * throws an exception if this is not a cell
     */
    public function checkCell() {
        if (!$this->isSingleCellByConstruction()) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("TableHeader - Not a single value"), $exception_config);
        }
    }

    /**
     * Clones this header...
     * Only usefull if you rename the table afterwards or if you set singleRowByConstruction.
     *
     * @return UniversalFilterTableHeader
     */
    public function cloneHeader() {
        return new UniversalFilterTableHeader(
                        $this->columns,
                        $this->isSingleRowByConstruction,
                        $this->isSingleColumnByConstruction);
    }

}

?>

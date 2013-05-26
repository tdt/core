<?php

/**
 * The content of the universal representation of a table
 *
 * @package The-Datatank/universalfilter/data
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\data;

use tdt\core\universalfilter\common\BigList;
use tdt\exceptions\TDTException;
use tdt\core\utility\Config;

class UniversalFilterTableContent {

    private $rows;
    private $size;
    public static $IDCOUNT = 0;
    private $needed;

    public function __construct() {
//        echo "<pre>";
//        var_dump(debug_backtrace());
//        echo "</pre>";
        $this->rows = new BigList();
        $this->size = 0;
        $this->needed = 0;
    }

    /**
     * Destroy the content of this table if no-one needs this table anymore...
     */
    public function tryDestroyTable() {
        if ($this->needed == 0) {
            if ($this->rows === null) {
                //debug_print_backtrace();
            } else {
                $this->rows->destroy();
                $this->rows = null;
            }
        }
    }

    /**
     * Tell this table: "I need this table"
     */
    public function tableNeeded() {
        $this->needed++;
    }

    /**
     * Get the row on a certain index
     * @param int $index
     * @return UniversalFilterTableContentRow
     */
    public function getRow($index) {
        if ($index < $this->size) {
            return $this->rows->getIndex($index);
        } else {
            // This can happen when limit(10) asks for 10 rows, but the table only contains 5
            // no worries, the exception is caught !
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("UniversalFilterTableContent: getRow: Index out of bounds"), $exception_config);
        }//should not happen
    }

    /**
     * Sets the row on a cetain index
     * @param int $index
     * @param UniversalFilterTableContentRow $row
     */
    public function setRow($index, $row) {
        if ($index < $this->size) {
            $this->rows->setIndex($index, $row);
        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("UniversalFilterTableContent: getRow: Index out of bounds"), $exception_config);
        }//should not happen
    }

    /**
     * Adds a row to this table
     * @param UniversalFilterTableContentRow $row
     */
    public function addRow($row) {
        $this->size++;
        $this->rows->addItem($row);
    }

    /**
     * Get a value of a column in a row
     * @param string $name
     * @param int $index
     */
    public function getValue($name, $index, $allowNull = false) {
        return $this->getRow($index)->getCellValue($name, $allowNull);
    }

    /**
     * Get the value of a column in the first row
     * @param string $name
     * @return string
     */
    public function getCellValue($name, $allowNull = false) {
        return $this->getValue($name, 0, $allowNull);
    }

    /**
     * Get the size of the table
     */
    public function getRowCount() {
        return $this->size;
    }

}
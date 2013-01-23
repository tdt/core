<?php

/**
 * A row in the content of the universal representation of a table
 *
 * @package The-Datatank/universalfilter/data
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\data;

include_once(__DIR__ . "/../common/HashString.php");

use tdt\core\utility\Config;
use tdt\exceptions\TDTException;

class UniversalFilterTableContentRow {

    private $data;

    public function __construct() {
        $this->data = new \stdClass();
    }

    /**
     * Defines a value on this row
     *
     * @param string $idOfField Which column?
     * @param ? $value What value?
     */
    public function defineValue($idOfField, $value) {
        if ($idOfField == "") {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Not a valid fieldname..."), $exception_config); // Can happen?
        }
        $this->data->$idOfField = array("value" => $value);
    }

    /**
     * Defines a value which represents a id
     * For the moment this is handled exactly the same as a value
     *
     * @param string $idOfField
     * @param ? $value
     */
    public function defineValueId($idOfField, $value) {
        if ($idOfField == "") {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Not a valid fieldname..."), $exception_config); // Can happen?
        }
        $this->data->$idOfField = array("id" => $value);
    }

    /**
     * Defines a grouped value on this row
     *
     * @param string $idOfField
     * @param UniversalTableContent $groupedColumnValues where each row has only one field "data" and is not grouped itself
     */
    public function defineGroupedValue($idOfField, $groupedColumnValues) {
        if ($idOfField == "") {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Not a valid fieldname..."), $exception_config); // Can happen?
        }
        $this->data->$idOfField = array("grouped" => $groupedColumnValues);
    }

    /**
     * returns the value of a field in the table
     *
     * Note: the value can be null. BUT you have to explicitlly allow null-values.
     * (To be sure you know you can get back null)
     */
    public function getCellValue($idOfField, $allowNull = false) {
        if (isset($this->data->$idOfField)) {
            $obj = $this->data->$idOfField;
            if (isset($obj["value"])) {
                //var_dump($obj["value"]);
                return $obj["value"];
            } else {
                if (isset($obj["id"])) {
                    return $obj["id"];
                } else {
                    if (isset($obj["grouped"])) {
                        debug_print_backtrace();
                        $exception_config = array();
                        $exception_config["log_dir"] = Config::get("general", "logging", "path");
                        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                        throw new TDTException(500, array("Error: Can not execute this operation on a grouped field!"), $exception_config);
                    } else {
                        // The value or id is null
                        if ($allowNull) {
                            return null;
                        } else {
                            return "";
                        }
                    }
                }
            }
        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Requested a unknown value on a row for a columnId: " . $idOfField . ""), $exception_config);
        }
    }

    /**
     * returns the grouped value of a field in the table
     * @return array
     */
    public function getGroupedValue($idOfField) {
        if (isset($this->data->$idOfField)) {
            $obj = $this->data->$idOfField;
            if (isset($obj["grouped"])) {
                return $obj["grouped"];
            } else {
                return null;
            }
        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Requested a unknown value on a row for a columnId " . $idOfField . ""), $exception_config);
        }
    }

    /**
     * returns a hash for the field. The hash is unique for the value!!! But does not contain special characters.
     * @param type $nameOfField
     * @return type
     */
    public function getHashForField($idOfField) {
        return hashWithNoSpecialChars($this->getCellValue($idOfField, false));
    }

    /**
     * Copy the value of a column from one row to another row
     * @param UniversalFilterTableContentRow $newRow
     * @param string $oldField
     * @param string $newField
     */
    public function copyValueTo(UniversalFilterTableContentRow $newRow, $oldField, $newField) {
        if (isset($this->data->$oldField)) {
            $newRow->data->$newField = $this->data->$oldField;
        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Requested a unknown value on a row for a columnId " . $oldField . ""), $exception_config);
        }
    }

}

?>

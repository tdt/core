<?php

/**
 * A row in the content of the universal representation of a table
 *
 * @package The-Datatank/universalfilter/data
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\spectql\implementation\data;

include_once(__DIR__ . "/../common/HashString.php");

class UniversalFilterTableContentRow
{

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
            \App::abort(500, "Not a valid fieldname...");
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
            \App::abort(500, "Not a valid fieldname...");
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
            \App::abort(500, "Not a valid fieldname...");
        }

        $this->data->$idOfField = array("grouped" => $groupedColumnValues);
    }

    /**
     * Returns the value of a field in the table
     *
     * Note: the value can be null. BUT you have to explicitally allow null-values
     */
    public function getCellValue($idOfField, $allowNull = false) {

        if (isset($this->data->$idOfField)) {

            $obj = $this->data->$idOfField;

            if (isset($obj["value"])) {

                return $obj["value"];
            } else {

                if (isset($obj["id"])) {
                    return $obj["id"];
                } else {
                    if (isset($obj["grouped"])) {

                        debug_print_backtrace();
                        \App::abort(500, "Error: Can not execute this operation on a grouped field!");
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

            \App::abort(500, "Requested a unknown value on a row for a columnId: " . $idOfField . "");
        }
    }

    /**
     * Returns the grouped value of a field in the table
     *
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

            \App::abort(500, "Requested a unknown value on a row for a columnId " . $idOfField . "");
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

            \App::abort(500, "Requested a unknown value on a row for a columnId " . $oldField . "");
        }
    }

}
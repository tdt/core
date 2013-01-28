<?php

/**
 * This class handles a CSV file
 *
 * @package The-Datatank/custom/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\strategies;

use Monolog\Logger;
use tdt\exceptions\TDTException;

class CSV extends ATabularData{
    
    private static $READ_MAX_AMOUNT_OF_ROWS = 100;
    // amount of chars in one row that can be read
    private static $MAX_LINE_LENGTH = 15000;

    /**
     * The parameters returned are required to make this strategy work.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateRequiredParameters() {
        return array("uri");
    }

    /**
     * @deprecated
     */
    public function documentUpdateParameters() {
        $this->parameters["uri"] = "The URI to the CSV file.";
        $this->parameters["PK"] = "The primary key of an entry. This must be the name of an existing column name in the CSV file.";
        $this->parameters["has_header_row"] = "If the CSV file contains a header row with the column name, pass 1 as value, if not pass 0. Default value is 1.";
        $this->parameters["delimiter"] = "The delimiter which is used to separate the fields that contain values, default value is a comma.";
        $this->parameters["start_row"] = "The number of the row (rows start at number 1) at which the actual data starts; i.e. if the first two lines are comment lines, your start_row should be 3. Default is 1.";
        return $this->parameters;
    }

    /**
     * The parameters ( array keys ) returned all of the parameters that can be used to create a strategy.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateParameters() {
        $this->parameters["uri"] = "The URI to the CSV file.";
        $this->parameters["PK"] = "The primary key of an entry. This must be the name of an existing column name in the CSV file.";
        $this->parameters["has_header_row"] = "If the CSV file contains a header row with the column name, pass 1 as value, if not pass 0. Default value is 1.";
        $this->parameters["delimiter"] = "The delimiter which is used to separate the fields that contain values, default value is a comma.";
        $this->parameters["start_row"] = "The number of the row (rows start at number 1) at which the actual data starts; i.e. if the first two lines are comment lines, your start_row should be 3. Default is 1.";
        return $this->parameters;
    }

    /**
     * Returns an array with parameter => documentation pairs that can be used to read a CSV resource.
     * @return array with parameter => documentation pairs
     */
    public function documentReadParameters() {
        return array();
    }

    /**
     * Read a resource
     * @param $configObject The configuration object containing all of the parameters necessary to read the resource.
     * @param $package The package name of the resource
     * @param $resource The resource name of the resource
     * @return $mixed An object created with fields of a CSV file.
     */
    public function read(&$configObject, $package, $resource) {
        
        /*
         * First retrieve the values for the generic fields of the CSV logic.
         * This is the uri to the file, and a parameter which states if the CSV file
         * has a header row or not.
         */

        parent::read($configObject, $package, $resource);
        $has_header_row = $configObject->has_header_row;
        $start_row = $configObject->start_row;
        $delimiter = $configObject->delimiter;

        /**
         * check if the uri is valid ( not empty )
         */
        if (isset($configObject->uri)) {
            $filename = $configObject->uri;
        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("Can't find URI of the CSV"), $exception_config);
        }

        $columns = $configObject->columns;
        $column_aliases = $configObject->column_aliases;
        $PK = $configObject->PK;

        $resultobject = array();
        $arrayOfRowObjects = array();
        $row = 0;

        $rows = array();
        $rowsRead = 0;
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE && $rowsRead <= CSV::$READ_MAX_AMOUNT_OF_ROWS + $start_row - 1) {
                $num = count($data);
                $csvRow = "";
                for ($c = 0; $c < $num; $c++) {
                    $csvRow = $csvRow . $delimiter . $this->enclose($data[$c]);
                }
                array_push($rows, ltrim($csvRow, $delimiter));
                $rowsRead++;
            }
            fclose($handle);
        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("Can't get any data from defined file ,$filename , for this resource."), $exception_config);
        }

        // get rid for the comment lines according to the given start_row
        for ($i = 1; $i < $start_row; $i++) {
            array_shift($rows);
        }


        $fieldhash = array();
        /**
         * loop through each row, and fill the fieldhash with the column names
         * if however there is no header, we fill the fieldhash beforehand
         * note that the precondition of the beforehand filling of the fieldhash
         * is that the column_name is an index! Otherwise there's no way of id'ing a column
         */
        if ($has_header_row == "0") {
            foreach ($columns as $index => $column_name) {
                $fieldhash[$column_name] = $index;
            }
        }

        $line = 0;

        foreach ($rows as $row => $fields) {
            $line++;
            $data = str_getcsv($fields, $delimiter, '"');

            // check if the delimiter exists in the csv file ( comes down to checking if the amount of fields in $data > 1 )
            if (count($data) <= 1 && $row == "") {
                $exception_config = array();
                $exception_config["log_dir"] = Config::get("general", "logging", "path");
                $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                throw new TDTException(452, array("The delimiter ( " . $delimiter . " ) wasn't present in the file, re-add the resource with the proper delimiter."), $exception_config);
            }

            /**
             * We support sparse trailing (empty) cells
             */
            if (count($data) != count($columns)) {
                if (count($data) < count($columns)) {
                    /**
                     * trailing empty cells
                     */
                    $missing = count($columns) - count($data);
                    for ($i = 0; $i < $missing; $i++) {
                        $data[] = "";
                    }
                } else if (count($data) > count($columns)) {
                    $line+= $start_row;
                    $amountOfElements = count($data);
                    $amountOfColumns = count($columns);
                    $exception_config = array();
                    $exception_config["log_dir"] = Config::get("general", "logging", "path");
                    $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                    throw new TDTException(452, array("The amount of data columns is larger than the amount of header columns from the csv, this could be because an incorrect delimiter (" . $delimiter . ") has been passed, or a corrupt datafile has been used. Line number of the error: $line. amount of columns - elements : $amountOfColumns - $amountOfElements."), $exception_config);
                }
            }

            // keys not found yet
            if (!count($fieldhash)) {

                // <<fast!>> way to detect empty fields
                // if it contains empty fields, it should not be our field hash
                $empty_elements = array_keys($data, "");

                if (!count($empty_elements)) {

                    // we found our key fields
                    for ($i = 0; $i < sizeof($data); $i++) {
                        /*
                         * format column names like they are stored, namely replace a whitespace by an underscore
                         */
                        $formatted_column = preg_replace('/\s+/', '_', trim($data[$i]));
                        $fieldhash[$formatted_column] = $i;
                    }

                    /*
                     * It could be that the CSV file has been changed column name wise.
                     * We're not going to throw an error for this, because it can still provide a representation of the actual CSV file's data
                     * but we're going to log it nonetheless.
                     */
                    foreach (array_keys($fieldhash) as $key) {
                        if (!in_array($key, $columns)) {
                            $log = new Logger('CSV');
                            $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ALERT));
                            $log->addAlert("$package/$resource : The column name $key that has been found in the CSV file isn't present in the saved columns of the CSV resource definition.");
                        }
                    }
                }
            } else {

                $rowobject = new \stdClass();
                $keys = array_keys($fieldhash);

                for ($i = 0; $i < sizeof($keys); $i++) {

                    $c = $keys[$i];

                    if (sizeof($columns) == 0 || !in_array($c, $columns)) {

                        $rowobject->$c = $data[$fieldhash[$c]];
                    } else if (in_array($c, $columns)) {

                        $rowobject->$column_aliases[$c] = $data[$fieldhash[$c]];
                    }
                }

                if ($PK == "") {
                    array_push($arrayOfRowObjects, $rowobject);
                } else {
                    if (!isset($arrayOfRowObjects[$rowobject->$PK]) && $rowobject->$PK != "") {
                        $arrayOfRowObjects[$rowobject->$PK] = $rowobject;
                    } elseif (isset($arrayOfRowObjects[$rowobject->$PK])) {
                        // this means the primary key wasn't unique !
                        $log = new Logger('CSV');
                        $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ALERT));
                        $log->addAlert("$package/$resource : The column name $key that has been found in the CSV file isn't present in the saved columns of the CSV resource definition.");
                    } else {
                        // this means the primary key was empty, log the problem and continue
                        $log = new Logger('CSV');
                        $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ALERT));
                        $log->addAlert("$package/$resource : The column name $key that has been found in the CSV file isn't present in the saved columns of the CSV resource definition.");
                    }
                }
            }
        }
        return $arrayOfRowObjects;
    }

    /**
     * encloses the $element in double quotes
     */
    private function enclose($element) {
        $element = rtrim($element, '"');
        $element = ltrim($element, '"');
        $element = '"' . $element . '"';
        return $element;
    }

    protected function isValid($package_id, $generic_resource_id) {

        if (!isset($this->columns)) {
            $this->columns = array();
        }

        if (!isset($this->column_aliases)) {
            $this->column_aliases = array();
        }

        if (!isset($this->has_header_row)) {
            $this->has_header_row = 1;
        }

        if (!isset($this->PK)) {
            $this->PK = "";
        }

        if (!isset($this->delimiter)) {
            $this->delimiter = ",";
        }

        if (!isset($this->start_row)) {
            $this->start_row = 1;
        }

        // has_header_row should be either 1 or 0
        if ($this->has_header_row != 0 && $this->has_header_row != 1) {
            $this->throwException($package_id, $generic_resource_id, "Header row should be either 1 or 0.");
        }

        /**
         * if no header row is given, then the columns that are being passed should be
         * int => something, int => something
         * if a header row is given however in the csv file, then we're going to extract those
         * header fields and put them in our back-end as well.
         */
        if ($this->has_header_row == "0") {
            // no header row ? then columns must be passed
            if (empty($this->columns)) {
                $this->throwException($package_id, $generic_resource_id, "Your array of columns must be an index => string hash array. Since no header row is specified in the resource CSV file.");
            }

            foreach ($this->columns as $index => $value) {
                if (!is_numeric($index)) {
                    $this->throwException($package_id, $generic_resource_id, "Your array of columns must be an index => string hash array.");
                }
            }
        } else {
            $fieldhash = array();
            if (($handle = fopen($this->uri, "r")) !== FALSE) {

                // for further processing we need to process the header row, this MUST be after the comments
                // so we're going to throw away those lines before we're processing our header_row
                // our first line will be processed due to lazy evaluation, if the start_row is the first one
                // then the first argument will return false, and being an &&-statement the second validation will not be processed
                $commentlinecounter = 1;
                while ($commentlinecounter < $this->start_row) {
                    $line = fgetcsv($handle, CSV::$MAX_LINE_LENGTH, $this->delimiter, '"');
                    $commentlinecounter++;
                }
                $index = 0;

                if (($line = fgetcsv($handle, CSV::$MAX_LINE_LENGTH, $this->delimiter, '"')) !== FALSE) {
                    // if no column aliases have been passed, then fill the columns variable
                    $index++;

                    if (count($line) <= 1) {
                        $exception_config = array();
                        $exception_config["log_dir"] = Config::get("general", "logging", "path");
                        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                        throw new TDTException(452, array("The delimiter ( " . $this->delimiter . " ) wasn't found in the first line of the file, perhaps the file isn't a CSV file or you passed along a wrong delimiter. On line $index."), $exception_config);
                    }

                    if (empty($this->columns)) {
                        for ($i = 0; $i < sizeof($line); $i++) {
                            $fieldhash[trim($line[$i])] = $i;
                            $this->columns[$i] = trim($line[$i]);
                        }
                    }
                } else {
                    $this->throwException($package_id, $generic_resource_id, $this->uri . " failed to get another line through the file handle with delimiter $this->delimiter.");
                }
                fclose($handle);
            } else {
                $this->throwException($package_id, $generic_resource_id, $this->uri . " could not open the file handle on the given location uri.");
            }
        }
        return true;
    }

}

?>

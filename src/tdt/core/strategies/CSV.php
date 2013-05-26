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

use tdt\exceptions\TDTException;
use tdt\core\model\resources\AResourceStrategy;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use tdt\core\utility\Config;
use tdt\core\model\resources\read\IFilter;
use tdt\core\universalfilter\interpreter\debugging\TreePrinter;
use tdt\core\universalfilter\interpreter\other\QueryTreeHandler;
use tdt\core\utility\LogicalInterpreter;
use tdt\core\model\ResourcesModel;

class CSV extends ATabularData implements IFilter{

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
        $page_size = AResourceStrategy::$DEFAULT_PAGE_SIZE;
        return array(
            "page" => "Represents the page number if the dataset is paged, this parameter works together with page_size, which is default set to $page_size. Set this parameter to -1 if you don't want paging to be applied.",
            "page_size" => "Represents the size of a page, this means that by setting this parameter, you can alter the amount of results that are returned, in one page (e.g. page=1&page_size=3 will give you results 1,2 and 3).",
            "limit" => "Instead of page/page_size you can use limit and offset. Limit has the same purpose as page_size, namely putting a cap on the amount of entries returned, the default is $page_size. Set this parameter to -1 if don't want paging to be applied.",
            "offset" => "Represents the offset from which results are returned (e.g. ?offset=12&limit=5 will return 5 results starting from 12).",
        );
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

        /**
         * Check the RESTparameters, for a database resource we know it's going to be tabular data
         * so RESTparameters cannot hold more than 2 strings, the first is the number of the item (rownum starting at 0), the second is the column name to select ( if present ofc.)
         */
        if(count($this->rest_params) > 2){

            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("The amount of REST parameters given, is too high. In a CSV resource, you can only give up to 2 REST parameters."), $exception_config);

        }else if(count($this->rest_params) > 0){
            if($this->rest_params[0] < 0){

               $exception_config = array();
               $exception_config["log_dir"] = Config::get("general", "logging", "path");
               $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
               throw new TDTException(452, array("The first REST parameter should be a positive integer."), $exception_config);
            }
        }

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

        /**
         * Get the columns from the configuration
         */
        $columns = $configObject->columns;
        $column_aliases = $configObject->column_aliases;
        $PK = $configObject->PK;

        $limit = $this->limit;
        $offset = $this->offset;

        $start_row = $configObject->start_row;
        $delimiter = $configObject->delimiter;

        // Read the CSV file.
        $resultobject = array();
        $arrayOfRowObjects = array();

        $rows = array();
        $total_rows = 0;

        $start_row = $configObject->start_row;
        if($configObject->has_header_row == 1){
            $start_row++;
        }

        $model = ResourcesModel::getInstance();
        $column_infos = $model->getColumnsFromResource($this->package,$this->resource);
        $aliases = array();

        foreach($column_infos as $column_info){
            $aliases[$column_info["column_name"]] = $column_info["column_name_alias"];
        }

        // Contains the amount of rows that we added to the resulting object.
        $hits = 0;
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {

                if($total_rows >= $start_row -1){
                    $num = count($data);

                    $values = $this->createValues($columns,$data,$total_rows);
                    if($offset <= $hits && $offset + $limit > $hits){
                        $obj = new \stdClass();

                        foreach($values as $key => $value){
                            $key = $aliases[$key];
                            if(!empty($key))
                                $obj->$key = $value;
                        }

                        if(empty($PK) || empty($aliases[$PK])){
                            array_push($arrayOfRowObjects,$obj);
                        }else{
                            $key = $aliases[$PK];
                            $arrayOfRowObjects[$obj->$key] = $obj;
                        }
                    }
                    $hits++;
                }
                $total_rows++;
            }
            fclose($handle);

        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("Can't get any data from defined file ,$filename , for this resource."), $exception_config);
        }

        // Paging.
        if($offset + $limit < $hits){
            $page = $offset/$limit;
            $page = round($page,0,PHP_ROUND_HALF_DOWN);
            if($page==0){
                $page = 1;
            }
            $this->setLinkHeader($page + 1,$limit,"next");

            $last_page = round($total_rows / $this->limit,0);
            if($last_page > $this->page+1){
                $this->setLinkHeader($last_page,$this->page_size, "last");
            }
        }

        if($offset > 0 && $hits >0){
            $page = $offset/$limit;
            $page = round($page,0,PHP_ROUND_HALF_DOWN);
            if($page==0){
                // Try to divide the paging into equal pages.
                $page = 2;
            }
            $this->setLinkHeader($page -1,$limit,"previous");
        }

        $result = $arrayOfRowObjects;
        if(count($this->rest_params) > 0){
            $result = array_shift($arrayOfRowObjects);
            if(count($this->rest_params) == 2){
                // add a column filter
                $column = $this->rest_params[1];

                // the uri is case insensitive, so the column might have been named with a uppercase (first) and result in a column not found.
                // so lets track down the "good" name of the column
                foreach(get_object_vars($result) as $property => $value){
                    if(strtolower($property) == $column){
                        $column = $property;
                    }
                }

                if(isset($result->$column)){
                    $result = $result->$column;
                }else{
                    $exception_config = array();
                    $exception_config["log_dir"] = Config::get("general", "logging", "path");
                    $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                    throw new TDTException(452, array("The column $column specified via the rest parameters wasn't found."), $exception_config);
                }
            }
        }

        return $result;
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

    /**
     * Implement the SPECTQL interface.
     */
    public function readAndProcessQuery($query, $parameters) {

        // Debug purposes
        /*$treePrinter = new TreePrinter();
        $tree = $treePrinter->treeToString($query);
        echo "<pre>";
        echo $tree;
        echo "</pre>";*/

        $queryHandler = new QueryTreeHandler($query);
        $converter = $queryHandler->getNoSqlConverter();

        // We're only executing the where clause.
        $queryNode = $queryHandler->getNodeForClause("where");

        // We only apply limit when no group by or sort is applied.
        $limit = AResourceStrategy::$DEFAULT_PAGE_SIZE;
        $offset = 0;

        $group_by = $converter->getGroupbyClause();
        $order = $converter->getOrderByClause();

        if(!empty($group_by) || !empty($order)){
            $limit = 2147483647; // max int
        }else{
            $limit_clause = $converter->getLimitClause();
            if(!empty($limit_clause)){
                $offset = $limit_clause[0];
                $limit = $limit_clause[1];
            }
        }

        if(empty($queryNode)){
            // Let the spectql tree handle the query
            $resultObject->indexInParent = "";
            $resultObject->executeNode = null;
            $resultObject->phpDataObject = null;
            $resultObject->parentNode = null;
            return $resultObject;
        }

        $configObject = $parameters["configObject"];
        parent::read($configObject, $parameters["package"], $parameters["resource"]);

        $resultObject = new \stdClass();

        // Get the where clause of the query.
        // The where clause contains statements i.e. subject operator value, so we still need to separate those to use them.
        $where = $converter->getWhereClause();

        $start_row = $configObject->start_row;
        $delimiter = $configObject->delimiter;

        if (isset($configObject->uri)) {
            $filename = $configObject->uri;
        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("Can't find URI of the CSV"), $exception_config);
        }

        // Get the columns from the configuration
        $columns = $configObject->columns;
        $PK = $configObject->PK;

        // Read the CSV file.
        $resultobject = array();
        $arrayOfRowObjects = array();

        $rows = array();
        $total_rows = 0;

        $start_row = $configObject->start_row;
        if($configObject->has_header_row == 1){
            $start_row++;
        }

        $model = ResourcesModel::getInstance();
        $column_infos = $model->getColumnsFromResource($this->package,$this->resource);
        $aliases = array();

        foreach($column_infos as $column_info){
            $aliases[$column_info["column_name"]] = $column_info["column_name_alias"];
        }

        // Contains the amount of rows that we added to the resulting object.
        $hits = 0;
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {

                if($total_rows >= $start_row -1){
                    $num = count($data);

                    // Only read the rows that are meant to be get (where clause).
                    $values = $this->createValues($columns,$data,$total_rows);
                    $interpret = new LogicalInterpreter();
                    if(!empty($where) && $interpret->interpret($where,$values)){
                        if($offset <= $hits && $offset + $limit > $hits){
                            $obj = new \stdClass();

                            foreach($values as $key => $value){
                                $key = $aliases[$key];
                                if(!empty($key))
                                    $obj->$key = $value;
                            }
                            array_push($arrayOfRowObjects,$obj);
                        }
                        $hits++;
                    }
                }
                $total_rows++;
            }
            fclose($handle);

        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("Can't get any data from defined file ,$filename , for this resource."), $exception_config);
        }

        // Paging.
        if($offset + $limit < $hits){
            $page = $offset/$limit;
            $page = round($page,0,PHP_ROUND_HALF_DOWN);
            if($page==0){
                $page = 1;
            }
            $this->setLinkHeader($page + 1,$limit,"next");

            $last_page = round($hits / $this->limit,0);
            if($last_page > $this->page+1){
                $this->setLinkHeader($last_page,$this->page_size, "last");
            }
        }

        if($offset > 0 && $hits >0){
            $page = $offset/$limit;
            $page = round($page,0,PHP_ROUND_HALF_DOWN);
            if($page==0){
                // Try to divide the paging into equal pages.
                $page = 2;
            }
            $this->setLinkHeader($page -1,$limit,"previous");
        }

        $result = $arrayOfRowObjects;
        if(count($this->rest_params) > 0){
            $result = array_shift($arrayOfRowObjects);
            if(count($this->rest_params) == 2){
                // add a column filter
                $column = $this->rest_params[1];

                // the uri is case insensitive, so the column might have been named with a uppercase (first) and result in a column not found.
                // so lets track down the "good" name of the column
                foreach(get_object_vars($result) as $property => $value){
                    if(strtolower($property) == $column){
                        $column = $property;
                    }
                }

                if(isset($result->$column)){
                    $result = $result->$column;
                }else{
                    $exception_config = array();
                    $exception_config["log_dir"] = Config::get("general", "logging", "path");
                    $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                    throw new TDTException(452, array("The column $column specified via the rest parameters wasn't found."), $exception_config);
                }
            }
        }

        $arrayOfRowObjects = $result;

        $resultObject->indexInParent = "";

        //We have executed the select partial tree, notify this to the universalfilterTableManager
        $resultObject->clause = "where";
        $resultObject->partialTreeResultObject = $arrayOfRowObjects;
        $resultObject->query = $query;


        return $resultObject;
    }

    /**
     * This function returns an array with key=column-name and value=data
     */
    private function createValues($columns,$data,$line_number = 0){

        if(count($data) > count($columns)){
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("The amount of data columns is larger than the amount of header columns from the csv, this could be because a bad delimiter is being used. Check your file at line: " . $line_number), $exception_config);
        }

        $result = array();
        foreach($columns as $index => $value){
            if(!empty($data[$index])){
                $result[$value] = $data[$index];
            }else{
                $result[$value] = "";
            }
        }
        return $result;
    }
}
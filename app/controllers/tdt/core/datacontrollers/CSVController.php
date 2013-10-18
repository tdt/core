<?php

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;

/**
 * CSV Controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class CSVController extends ADataController {

    // amount of chars in one row that can be read
    private static $MAX_LINE_LENGTH = 0;


    public function readData($source_definition, $parameters = null){

        list($limit, $offset) = $this->calculateLimitAndOffset();

        // Check URI given.
        if (!empty($source_definition->uri)) {
            $uri = $source_definition->uri;
        } else {
            \App::abort(452, "Can't find URI of the CSV");
        }

        // Get data from definition
        $has_header_row = $source_definition->has_header_row;
        $start_row = $source_definition->start_row;
        $delimiter = $source_definition->delimiter;
        $PK = $source_definition->pk;

        // Get CSV columns
        $columns = $source_definition->tabularColumns();
        $columns = $columns->getResults();

        if(!$columns){
            \App::abort(452, "Can't find or fetch the columns for this CSV file.");
        }

        // Set aliases.
        $aliases = array();
        foreach($columns as $column){
            $aliases[$column->column_name] = $column->column_name_alias;
        }

        // Read the CSV file.
        $resultobject = array();
        $arrayOfRowObjects = array();

        $rows = array();
        $total_rows = 0;

        if($has_header_row == 1){
            $start_row++;
        }

        // Contains the amount of rows that we added to the resulting object.
        $hits = 0;
        if (($handle = fopen($uri, "r")) !== FALSE) {

            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {

                if($total_rows >= $start_row -1){
                    $num = count($data);

                    $values = $this->createValues($columns, $data, $total_rows);
                    if($offset <= $hits && $offset + $limit > $hits){
                        $obj = new \stdClass();

                        foreach($values as $key => $value){

                            $key = $aliases[$key];
                            if(!empty($key))
                                $obj->$key = $value;
                        }

                        if(empty($PK) || empty($aliases[$PK])){
                            array_push($arrayOfRowObjects, $obj);
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
            \App::abort(452, "Can't get any data from defined URI ($uri) for this resource.");
        }

        $paging = array();

        // Calculate the paging parameters and pass them with the data object.
        if($offset + $limit < $hits){
            $page = $offset/$limit;
            $page = round($page,0,PHP_ROUND_HALF_DOWN);

            if($page==0){
                $page = 1;
            }

            $paging['next'] = array($page + 1, $limit);

            $last_page = round($total_rows / $limit,0);

            if($last_page > $page + 1){
                $paging['last'] = array($last_page, self::$DEFAULT_PAGE_SIZE);
            }
        }

        if($offset > 0 && $hits > 0){

            $page = $offset/$limit;
            $page = round($page, 0, PHP_ROUND_HALF_DOWN);

            if($page == 0){

                // Try to divide the paging into equal pages.
                $page = 2;
            }

            $paging['previous'] = array($page - 1, $limit);
        }

        $result = $arrayOfRowObjects;

        $data_result = new Data();
        $data_result->data = $result;
        $data_result->paging = $paging;

        return $data_result;
    }

    /**
     * This function returns an array with key=column-name and value=data
     */
    private function createValues($columns, $data, $line_number = 0){

        $result = array();
        foreach($columns as $column){
            if(!empty($data[$column->index])){
                $result[$column->column_name_alias] = $data[$column->index];
            }else{
                $result[$value] = "";
            }
        }
        return $result;
    }

}
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

    // Amount of characters in one row that can be read
    private static $MAX_LINE_LENGTH = 0;


    public function readData($source_definition, $rest_parameters = array()){

        list($limit, $offset) = $this->calculateLimitAndOffset();

        // Check the given URI
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
        $columns = $source_definition->tabularColumns()->getResults();

        // Get the geo properties
        $geo_properties = $source_definition->geoProperties()->getResults();
        $geo = array();

        foreach($geo_properties as $geo_prop){
            $geo[$geo_prop->property] = $geo_prop->path;
        }

        if(!$columns){
            \App::abort(452, "Can't find or fetch the columns for this CSV file.");
        }

        // Create aliases for the columns
        $aliases = array();
        $pk = null;

        foreach($columns as $column){
            $aliases[$column->column_name] = $column->column_name_alias;

            if(!empty($column->is_pk)){
                $pk = $column->column_name_alias;
            }
        }

        // Read the CSV file
        $resultobject = array();
        $row_objects = array();

        $rows = array();
        $total_rows = 0;

        if($has_header_row == 1){
            $start_row++;
        }

        // Contains the amount of rows that we added to the resulting object
        $hits = 0;
        if (($handle = fopen($uri, "r")) !== FALSE) {

            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {

                if($total_rows >= $start_row){

                    $num = count($data);

                    // Create the values array, containing the (aliased) name of the column
                    // to the value of a the row which $data represents
                    $values = $this->createValues($columns, $data);
                    if($offset <= $hits && $offset + $limit > $hits){

                        $obj = new \stdClass();

                        foreach($values as $key => $value){
                            $obj->$key = $value;
                        }

                        if(empty($pk)){
                            array_push($row_objects, $obj);
                        }else{

                            if(!empty($row_objects[$obj->$pk])){
                                \Log::info("The primary key $pk has been used already for another record!");
                            }else{
                                $row_objects[$obj->$pk] = $obj;
                            }

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

        $paging = $this->calculatePagingHeaders($limit, $offset, $total_rows);

        $data_result = new Data();
        $data_result->data = $row_objects;
        $data_result->paging = $paging;
        $data_result->geo = $geo;

        return $data_result;
    }

    /**
     * This function returns an array with key=column-name and value=data.
     */
    private function createValues($columns, $data){

        $result = array();

        foreach($columns as $column){
            if(!empty($data[$column->index]) || is_numeric($data[$column->index])){
                $result[$column->column_name_alias] = $data[$column->index];
            }else{
                \App::abort(452, "The index $column->index could not be found in the data file. Indices start at 0.");
            }
        }

        return $result;
    }
}

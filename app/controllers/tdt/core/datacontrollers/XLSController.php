<?php

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;
use PHPExcel_IOFactory as IOFactory;

/**
 * Excell Controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class XLSController extends ADataController {

    public function readData($source_definition, $rest_parameters = array()){

        list($limit, $offset) = self::calculateLimitAndOffset();

        $uri = $source_definition->uri;
        $sheet = $source_definition->sheet;
        $has_header_row = $source_definition->has_header_row;
        // Rows start at 1 in XLS, we have however documented that they start at 0 to be consistent with common sense and other
        // tabular sources such as CSV.
        $start_row = $source_definition->start_row + 1;

        // Retrieve the columns from XLS
        $columns = $source_definition->tabularColumns()->getResults();

        if(!$columns){
            \App::abort(500, "Cannot find the columns from the XLS definition.");
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

        // Create an array to store our objects in to return
        $row_objects = array();

        // Get the temporary directory to store our excel files in if necessary
        $tmp_path = sys_get_temp_dir();

        if(empty($tmp_path)){
            \App::abort(500, "The temp directory, retrieved by the operating system, could not be retrieved.");
        }

        try {

            if (substr($uri , 0, 4) == "http") {

                $tmpFile = uniqid();
                file_put_contents($tmp_path . "/" . $tmpFile, file_get_contents($uri));
                $php_obj = self::loadExcel($tmp_path . "/" . $tmpFile, $this->getFileExtension($uri), $sheet);

            } else {
                $php_obj = self::loadExcel($uri, $this->getFileExtension($uri), $sheet);
            }

            if(empty($php_obj)){
                \App::abort(500, "The Excel file could not be retrieved from the location $uri.");
            }

            $worksheet = $php_obj->getSheetByName($sheet);

            if(empty($worksheet)){
                \App::abort(500, "The worksheet $sheet could not be found in the Excel file located on $uri.");
            }

            // The amount of rows added to the result
            $total_rows = 0;

            if($has_header_row == 1){
                $start_row++;
            }

            // Iterate all the rows of the Excell sheet
            foreach ($worksheet->getRowIterator() as $row) {

                $row_index = $row->getRowIndex();

                // If our offset is ok, start parsing the data from the excell sheet
                if($row_index > $start_row) {

                    $cell_iterator = $row->getCellIterator();
                    $cell_iterator->setIterateOnlyExistingCells(false);

                    // Only read rows that are allowed in the current requested page
                   if($offset <= $total_rows && $offset + $limit > $total_rows){

                        $rowobject = new \stdClass();

                        // Iterate each cell in the row, create an array of the values with the name of the column
                        // Indices start from 1 in the Excel API
                        $data = array();
                        foreach ($cell_iterator as $cell) {
                            $data[$cell->columnIndexFromString($cell->getColumn()) - 1] = $cell->getCalculatedValue();
                        }

                        $values = $this->createValues($columns, $data);

                        foreach($values as $key => $value){
                            $rowobject->$key = $value;
                        }

                        if(empty($pk)) {
                            array_push($row_objects,$rowobject);
                        } else {
                            if(empty($row_objects[$rowobject->$pk])){
                                $row_objects[$rowobject->$pk] = $rowobject;
                            }elseif(!empty($row_objects[$rowobject->$pk])){

                                $double = $rowobject->$pk;
                                \Log::info("The primary key $double has been used already for another record!");
                            }else{

                                $double = $rowobject->$pk;
                                \Log::info("The primary key $double is empty.");
                            }
                        }
                    }
                    $total_rows++;
                }
            }

            $php_obj->disconnectWorksheets();

            $paging = $this->calculatePagingHeaders($limit, $offset, $total_rows);

            $data_result = new Data();
            $data_result->data = $row_objects;
            $data_result->paging = $paging;

            return $data_result;

        } catch( Exception $ex) {
            App::abort(500, "Failed to retrieve data from the XLS file on location $uri.");
        }
    }

    /**
     * Retrieve the file extension from the xls file. (xls or xlsx)
     */
    public static function getFileExtension($file){
        return strtolower(substr(strrchr($file,'.'), 1));
    }

    /**
     * Create an Excel PHP Reader object from the Excel sheet.
     */
    public static function loadExcel($file, $type, $sheet) {

        if($type == "xls") {
            $objReader = IOFactory::createReader('Excel5');
        }else if($type == "xlsx") {
            $objReader = IOFactory::createReader('Excel2007');
        }else{
            \App::abort(500, "The given file is not supported, supported file are xls or xlsx files.");
        }

        $objReader->setReadDataOnly(true);
        $objReader->setLoadSheetsOnly($sheet);

        return $objReader->load($file);
    }

    /**
     * This function returns an array with key=column-name and value=data given
     * a certain data row.
     */
    private function createValues($columns, $data){

        $result = array();

        foreach($columns as $column){
            if(!empty($data[$column->index]) || is_numeric(@$data[$column->index])){
                $result[$column->column_name_alias] = utf8_encode($data[$column->index]);
            }else{
                \App::abort(500, "The index $column->index could not be found in the XLS file. Index count starts at 0.");
            }
        }

        return $result;
    }
}

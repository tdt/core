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

    public function readData($source_definition, $parameters = null){

        list($limit, $offset) = $this->calculateLimitAndOffset();

        $uri = $source_definition->uri;
        $sheet = $source_definition->sheet;
        $has_header_row = $source_definition->has_header_row;
        $start_row = $source_definition->start_row;
        $pk = $source_definition->pk;

        // Retrieve the columns from XLS.
        $columns_obj = $source_definition->tabularColumns();
        $columns_obj = $columns_obj->getResults();

        if(!$columns_obj){
            \App::abort(452, "Can't find or fetch the columns for this Excell file.");
        }

        // Set aliases
        $aliases = array();
        $columns = array();
        $fieldhash = array();

        foreach($columns_obj as $column){
            $aliases[$column->column_name] = $column->column_name_alias;
            array_push($columns, $column->column_name);
            $fieldhash[$column->column_name] = $column->index;

            if($column->is_pk){
                $pk = $column->column_name_alias;
            }
        }

        $arrayOfRowObjects = array();

        $tmp_path = sys_get_temp_dir();

        if(empty($tmp_path)){
            \App::abort(452, "The temporary file of the system cannot be found or used.");
        }

        try {

            if (substr($uri , 0, 4) == "http") {

                $tmpFile = uniqid();
                file_put_contents($tmp_path . "/" . $tmpFile, file_get_contents($uri));
                $objPHPExcel = self::loadExcel($tmp_path . "/" . $tmpFile, $this->getFileExtension($uri),$sheet);
            } else {
                $objPHPExcel = self::loadExcel($uri, $this->getFileExtension($uri),$sheet);
            }

            if(empty($objPHPExcel)){
                \App::abort(452, "The Excel file could not be loaded from $uri.");
            }

            $worksheet = $objPHPExcel->getSheetByName($sheet);

            if(empty($worksheet)){
                \App::abort(452, "The worksheet $sheet could not be found in the Excel file.");
            }

            // The amount of rows added to the result.
            $hits = 0;
            $total_rows = 0;

            foreach ($worksheet->getRowIterator() as $row) {

                $row_index = $row->getRowIndex();

                if ($row_index >= $start_row) {

                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    if ($row_index == $start_row && $has_header_row == "1") {

                        foreach ($cellIterator as $cell) {

                            if(!is_null($cell) && $cell->getCalculatedValue() != ""){
                                $column_index = $cell->columnIndexFromString($cell->getColumn());
                                $fieldhash[ $cell->getCalculatedValue() ] = $column_index;
                            }
                        }
                    } else if($offset <= $total_rows && $offset + $limit > $total_rows){

                        $rowobject = new \stdClass();
                        $keys = array_keys($fieldhash);

                        foreach ($cellIterator as $cell) {

                            $column_index = $cell->columnIndexFromString($cell->getColumn());

                            if (!is_null($cell) && isset($keys[$column_index-1]) ) {

                                // Format the column name as we normally format column names
                                $c = $keys[$column_index - 1];
                                $c = trim($c);
                                $c = preg_replace('/\s+/', '_', $c);
                                $c = strtolower($c);

                                if(in_array($c,$columns)){
                                    $rowobject->$aliases[$c] = $cell->getCalculatedValue();
                                }
                            }
                        }

                        if(empty($pk)) {
                            array_push($arrayOfRowObjects,$rowobject);
                        } else {
                            if(!isset($arrayOfRowObjects[$rowobject->$pk]) && $rowobject->$pk != ""){
                                $arrayOfRowObjects[$rowobject->$pk] = $rowobject;
                            }elseif(!empty($arrayOfRowObjects[$rowobject->$pk])){
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

            $objPHPExcel->disconnectWorksheets();

            $data_result = new Data();
            $data_result->data = $arrayOfRowObjects;

            return $data_result;

        } catch( Exception $ex) {
            App::abort(452, "Failed to retrieve data from the XLS file with path $uri.");
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
            \App::abort(452, "The given file is not supported, supported file are xls or xlsx files.");
        }

        $objReader->setReadDataOnly(true);
        $objReader->setLoadSheetsOnly($sheet);

        return $objReader->load($file);
    }
}
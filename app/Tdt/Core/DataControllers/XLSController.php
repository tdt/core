<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface;
use PHPExcel_IOFactory as IOFactory;

/**
 * Excel Controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class XLSController extends ADataController
{

    private $tabular_columns;
    private $geo_properties;

    public function __construct(TabularColumnsRepositoryInterface $tabular_columns, GeoPropertyRepositoryInterface $geo_properties)
    {
        $this->tabular_columns = $tabular_columns;
        $this->geo_properties = $geo_properties;
    }

    public function readData($source_definition, $rest_parameters = array())
    {
        list($limit, $offset) = Pager::calculateLimitAndOffset();

        // Disregard the paging when rest parameters are given
        if (!empty($rest_parameters)) {
            $limit = PHP_INT_MAX;
            $offset = 0;
        }

        $uri = $source_definition['uri'];
        $sheet = $source_definition['sheet'];
        $has_header_row = $source_definition['has_header_row'];

        // Rows start at 1 in XLS, we have however documented that they start at 0 to be consistent with common sense and other
        // tabular sources such as CSV.
        $start_row = $source_definition['start_row'] + 1;

        // Retrieve the columns from XLS
        $columns = $this->tabular_columns->getColumns($source_definition['id'], 'XlsDefinition');

        if (empty($columns)) {
            \App::abort(500, "Cannot find the columns from the XLS definition.");
        }

        // Create aliases for the columns
        $aliases = $this->tabular_columns->getColumnAliases($source_definition['id'], 'XlsDefinition');
        $pk = null;

        foreach ($columns as $column) {

            if (!empty($column['is_pk'])) {
                $pk = $column['column_name_alias'];
            }
        }

        // Create an array to store our objects in to return
        $row_objects = array();

        // Get the temporary directory to store our excel files in if necessary
        $tmp_path = sys_get_temp_dir();

        if (empty($tmp_path)) {
            \App::abort(500, "The temp directory, retrieved by the operating system, could not be retrieved.");
        }

        try {

            if (substr($uri, 0, 4) == "http") {

                $tmpFile = uniqid();
                file_put_contents($tmp_path . "/" . $tmpFile, file_get_contents($uri));
                $php_obj = self::loadExcel($tmp_path . "/" . $tmpFile, $this->getFileExtension($uri), $sheet);

            } else {
                $php_obj = self::loadExcel($uri, $this->getFileExtension($uri), $sheet);
            }

            if (empty($php_obj)) {
                \App::abort(500, "The Excel file could not be retrieved from the location $uri.");
            }

            $worksheet = $php_obj->getSheetByName($sheet);

            if (empty($worksheet)) {
                \App::abort(500, "The worksheet $sheet could not be found in the Excel file located on $uri.");
            }

            // The amount of rows added to the result
            $total_rows = 0;

            if ($has_header_row == 1) {
                $start_row++;
            }

            // Iterate all the rows of the Excell sheet
            foreach ($worksheet->getRowIterator() as $row) {

                $row_index = $row->getRowIndex();

                // If our offset is ok, start parsing the data from the excell sheet
                if ($row_index > $start_row) {

                    $cell_iterator = $row->getCellIterator();
                    $cell_iterator->setIterateOnlyExistingCells(false);

                    // Only read rows that are allowed in the current requested page
                    if ($offset <= $total_rows && $offset + $limit > $total_rows) {

                        $rowobject = new \stdClass();

                        // Iterate each cell in the row, create an array of the values with the name of the column
                        // Indices start from 1 in the Excel API
                        $data = array();
                        foreach ($cell_iterator as $cell) {
                            $data[$cell->columnIndexFromString($cell->getColumn()) - 1] = $cell->getCalculatedValue();
                        }

                        $values = $this->createValues($columns, $data);

                        foreach ($values as $key => $value) {
                            $rowobject->$key = $value;
                        }

                        if (empty($pk)) {
                            array_push($row_objects, $rowobject);
                        } else {
                            if (empty($row_objects[$rowobject->$pk])) {
                                $row_objects[$rowobject->$pk] = $rowobject;
                            } elseif (!empty($row_objects[$rowobject->$pk])) {

                                $double = $rowobject->$pk;
                                \Log::info("The primary key $double has been used already for another record!");
                            } else {

                                $double = $rowobject->$pk;
                                \Log::info("The primary key $double is empty.");
                            }
                        }
                    }
                    $total_rows++;
                }
            }

            $php_obj->disconnectWorksheets();

            $paging = Pager::calculatePagingHeaders($limit, $offset, $total_rows);

            $data_result = new Data();
            $data_result->data = $row_objects;
            $data_result->paging = $paging;
            $data_result->preferred_formats = $this->getPreferredFormats();

            return $data_result;

        } catch (Exception $ex) {
            App::abort(500, "Failed to retrieve data from the XLS file on location $uri.");
        }
    }

    /**
     * Retrieve the file extension from the xls file. (xls or xlsx)
     */
    public static function getFileExtension($file)
    {
        return strtolower(substr(strrchr($file, '.'), 1));
    }

    /**
     * Create an Excel PHP Reader object from the Excel sheet.
     */
    public static function loadExcel($file, $type, $sheet)
    {

        if ($type == "xls") {
            $objReader = IOFactory::createReader('Excel5');
        } elseif ($type == "xlsx") {
            $objReader = IOFactory::createReader('Excel2007');
        } else {
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
    private function createValues($columns, $data)
    {
        $result = array();

        foreach ($columns as $column) {

            $value = @$data[$column['index']];

            if (!is_null($value)) {
                $result[$column['column_name_alias']] = $data[$column['index']];
            } else {
                $index = $column['index'];

                \Log::warning("The column $index contained an empty value in the XLS file.");
                $result[$column['column_name_alias']] = '';
            }
        }

        return $result;
    }

    /**
     * Retrieve colummn information from the request parameters.
     */
    public static function parseColumns($input)
    {
        $columns_info = @$input['columns'];
        $pk = @$input['pk'];

        $aliases = array();

        if (!empty($columns_info)) {
            foreach ($columns_info as $column_info) {
                $aliases[$column_info['index']] = $column_info['column_name_alias'];
            }
        }

        $columns = array();
        $tmp_dir = sys_get_temp_dir();

        if (empty($columns)) {

            if (!is_dir($tmp_dir)) {
                mkdir($tmp_dir);
            }

            $is_uri = (substr($input['uri'], 0, 4) == "http");

            try {
                if ($is_uri) {

                    $tmp_file = uniqid();

                    file_put_contents($tmp_dir. "/" . $tmp_file, file_get_contents($input['uri']));
                    $php_obj = self::loadExcel($tmp_dir ."/" . $tmp_file, self::getFileExtension($input['uri']), $input['sheet']);
                } else {
                    $php_obj = self::loadExcel($input['uri'], self::getFileExtension($input['uri']), $input['sheet']);
                }

                $worksheet = $php_obj->getSheetByName($input['sheet']);

            } catch (Exception $ex) {
                $uri = $input['uri'];
                \App::abort(404, "Something went wrong whilst retrieving the Excel file from uri $uri.");
            }


            if (is_null($worksheet)) {
                $sheet = $input['sheet'];
                \App::abort(404, "The sheet with name, $sheet, has not been found in the Excel file.");
            }

            foreach ($worksheet->getRowIterator() as $row) {

                $row_index = $row->getRowIndex();

                // Rows start at 1 in XLS
                if ($row_index == $input['start_row'] + 1) {

                    $cell_iterator = $row->getCellIterator();
                    $cell_iterator->setIterateOnlyExistingCells(false);

                    $column_index = 0;

                    foreach ($cell_iterator as $cell) {

                        $column_name = '';

                        if ($cell->getValue() != "") {

                            $column_name = trim($cell->getCalculatedValue());

                        } else {

                            $column_name = 'column_' . $column_index;

                        }

                        // Try to get an alias from the options, if it's empty
                        // then just take the column value as alias
                        $alias = @$aliases[$column_index];

                        if (empty($alias)) {
                            $alias = $column_name;
                        }

                        array_push(
                            $columns,
                            array(
                            'index' => $column_index,
                            'column_name' => $column_name,
                            'column_name_alias' => $alias,
                            'is_pk' => ($pk === $column_index)
                            )
                        );

                        $column_index++;
                    }

                    break;
                }
            }

            $php_obj->disconnectWorksheets();

            if ($is_uri) {
                unlink($tmp_dir . "/" . $tmp_file);
            }
        }

        return $columns;
    }
}

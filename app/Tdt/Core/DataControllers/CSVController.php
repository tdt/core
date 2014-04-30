<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface;

/**
 * CSV Controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class CSVController extends ADataController
{
    // Amount of characters in one row that can be read
    private static $MAX_LINE_LENGTH = 0;

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

        // Check the given URI
        if (!empty($source_definition['uri'])) {
            $uri = $source_definition['uri'];
        } else {
            \App::abort(500, "No location of the CSV file has been passed, this is most likely due to a corrupt CSV definition.");
        }

        // Get data from definition
        $has_header_row = $source_definition['has_header_row'];
        $start_row = $source_definition['start_row'];
        $delimiter = $source_definition['delimiter'];

        // Get CSV columns
        $columns = $this->tabular_columns->getColumns($source_definition['id'], 'CsvDefinition');

        // Get the geo properties
        $geo_properties = $this->geo_properties->getGeoProperties($source_definition['id'], 'CsvDefinition');

        $geo = array();

        foreach ($geo_properties as $geo_prop) {
            $geo[$geo_prop['property']] = $geo_prop['path'];
        }

        if (!$columns) {
            // 500 error because this shouldn't happen in normal conditions
            // Columns are parsed upon adding a CSV resource and are always present
            \App::abort(500, "Cannot find the columns of the CSV file, this might be due to a corrupted database or because columns weren't added upon creating the CSV definition.");
        }

        // Create aliases for the columns
        $aliases = $this->tabular_columns->getColumnAliases($source_definition['id'], 'CsvDefinition');
        $pk = null;

        foreach ($columns as $column) {

            if (!empty($column['is_pk'])) {
                $pk = $column['column_name_alias'];
            }
        }

        // Read the CSV file
        $resultobject = array();
        $row_objects = array();

        $rows = array();
        $total_rows = 0;

        if ($has_header_row == 1) {
            $start_row++;
        }

        // Contains the amount of rows that we added to the resulting object
        $hits = 0;

        if (($handle = fopen($uri, "r")) !== false) {

            while (($data = fgetcsv($handle, 2000000, $delimiter)) !== false) {

                if ($total_rows >= $start_row) {

                    $num = count($data);

                    // Create the values array, containing the (aliased) name of the column
                    // to the value of a the row which $data represents
                    $values = $this->createValues($columns, $data);
                    if ($offset <= $hits && $offset + $limit > $hits) {

                        $obj = new \stdClass();

                        foreach ($values as $key => $value) {
                            $obj->$key = $value;
                        }

                        if (empty($pk)) {
                            array_push($row_objects, $obj);
                        } else {

                            if (!empty($row_objects[$obj->$pk])) {
                                \Log::info("The primary key $pk has been used already for another record!");
                            } else {
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
            \App::abort(500, "Cannot retrieve any data from the CSV file on location $uri.");
        }

        $paging = Pager::calculatePagingHeaders($limit, $offset, $total_rows);

        $data_result = new Data();
        $data_result->data = $row_objects;
        $data_result->paging = $paging;
        $data_result->geo = $geo;
        $data_result->preferred_formats = $this->getPreferredFormats();

        return $data_result;
    }

    /**
     * This function returns an array with key=column-name and value=data.
     */
    private function createValues($columns, $data)
    {

        $result = array();

        foreach ($columns as $column) {
            if (!empty($data[$column['index']]) || is_numeric(@$data[$column['index']])) {
                $result[$column['column_name_alias']] = @$data[$column['index']];
            } else {

                $index = $column['index'];

                \Log::warning("We expected a value for index $index, yet no value was given. Filling in an empty value.");

                $result[$column['column_name_alias']] = null;
            }
        }

        return $result;
    }

    /**
     * Parse the columns from a CSV file and return them
     * Optionally aliases can be given to columns as well as a primary key
     */
    public static function parseColumns($config)
    {
        // Get the columns out of the csv file before saving the csv definition
        // If columns are being passed using the json body or request parameters
        // allow them to function as aliases, aliases have to be passed as index (0:n-1) => alias
        $columns_info = @$config['columns'];
        $pk = @$config['pk'];

        $aliases = array();

        if (!empty($columns_info)) {
            foreach ($columns_info as $column_info) {
                $aliases[$column_info['index']] = $column_info['column_name_alias'];
            }
        }

        $columns = array();

        if (($handle = fopen($config['uri'], "r")) !== false) {

            // Throw away the lines untill we hit the start row
            // from then on, process the columns
            $commentlinecounter = 0;

            while ($commentlinecounter < $config['start_row']) {
                $line = fgetcsv($handle, 0, $config['delimiter'], '"');
                $commentlinecounter++;
            }

            $index = 0;

            if (($line = fgetcsv($handle, 0, $config['delimiter'], '"')) !== false) {

                if (sizeof($line) <= 1) {

                    $delimiter = $config['delimiter'];
                    $uri = $config['uri'];

                    \App::abort(400, "The delimiter ($delimiter) wasn't found, make sure the passed delimiter is the one that is used in the CSV file on location $uri.");
                }

                $index++;

                for ($i = 0; $i < sizeof($line); $i++) {

                    // Try to get an alias from the config, if it's empty
                    // then just take the column value as alias
                    $alias = @$aliases[$i];

                    if (empty($alias)) {
                        $alias = trim($line[$i]);
                    }

                    array_push(
                        $columns,
                        array(
                            'index' => $i,
                            'column_name' => trim($line[$i]),
                            'column_name_alias' => $alias,
                            'is_pk' => ($pk === $i)
                        )
                    );
                }
            } else {
                \App::abort(400, "The columns could not be retrieved from the csv file on location $uri.");
            }

            fclose($handle);
        } else {
            \App::abort(400, "The columns could not be retrieved from the csv file on location $uri.");
        }

        return $columns;
    }
}

<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface;
use Illuminate\Database\QueryException;

/**
 * MySQL controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class MYSQLController extends ADataController
{
    private $columnsRepo;

    public function __construct(TabularColumnsRepositoryInterface $columnsRepo, GeoPropertyRepositoryInterface $geoRepo)
    {
        $this->columnsRepo = $columnsRepo;
        $this->geoRepo = $geoRepo;
    }

    public function readData($source_definition, $rest_parameters = array())
    {
        //Check if the query is already paged.
        $limitInQuery = false;

        if (stripos($source_definition['query'], 'limit')) {
            $limitInQuery = true;
        } else {
            list($limit, $offset) = Pager::calculateLimitAndOffset();
        }

        // Disregard the paging when rest parameters are given
        if (! empty($rest_parameters)) {
            $limit = 500;
            $offset = 0;
        }

        // Get the columns from the repository
        $columns = $this->columnsRepo->getColumns($source_definition['id'], 'MysqlDefinition');

        // Get the geo properties
        $geo_properties = $this->geoRepo->getGeoProperties($source_definition['id'], 'MysqlDefinition');

        $geo = array();

        foreach ($geo_properties as $geo_prop) {
            $geo[$geo_prop['property']] = $geo_prop['path'];
        }

        if (! $columns) {
            // 500 error because this shouldn't happen in normal conditions
            // Columns are parsed upon adding a CSV resource and are always present
            \App::abort(500, 'Cannot find the columns of the MySQL table file, this might be due to a corrupted database or a broken configuration.');
        }

        // Create aliases for the columns
        $aliases = $this->columnsRepo->getColumnAliases($source_definition['id'], 'MysqlDefinition');
        $pk = null;

        foreach ($columns as $column) {
            if (! empty($column['is_pk'])) {
                $pk = $column['column_name_alias'];
            }
        }

        // Connect to the database
        $db_config = array(
            'charset'   => 'utf8',
            'collation' => $source_definition['collation'],
            'database'  => $source_definition['database'],
            'driver'    => 'mysql',
            'host'      => $source_definition['mysql_host'],
            'password'  => $source_definition['mysql_password'],
            'port' => $source_definition['mysql_port'],
            'username'  => $source_definition['mysql_username'],
        );

        // Configure a connection
        \Config::set('database.connections.mysqltmp', $db_config);

        // Make a database connection
        $db = \DB::connection('mysqltmp');

        try {
            $query = $source_definition['query'];

            // Get the total amount of records for the query for pagination
            preg_match('/select.*?(from.*)/msi', $query, $matches);

            if (empty($matches[1])) {
                \App::abort(400, 'Failed to make a count statement, make sure the SQL query is valid.');
            }

            $count_query = 'select count(*) as count ' . $matches[1];

            $count_result = $db->select($count_query);

            $total_rows = $count_result[0]->count;

            if (! $limitInQuery) {
                if (! empty($limit)) {
                    $query .= ' limit ' . $limit;
                }

                if (! empty($offset)) {
                    $query .= ' offset ' . $offset;
                }
            }

            $result = $db->select($query);
        } catch (QueryException $ex) {
            \App::abort(400, 'A bad query has been made, make sure all passed statements are SQL friendly. The error message was: ' . $ex->getMessage());
        }

        // Get the paging headers
        $paging = Pager::calculatePagingHeaders($limit, $offset, $total_rows);

        $data_result = new Data();
        $data_result->data = $result;
        $data_result->paging = $paging;
        $data_result->geo = $geo;
        $data_result->preferred_formats = $this->getPreferredFormats();

        return $data_result;
    }
}

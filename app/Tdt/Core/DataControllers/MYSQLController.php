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
        list($limit, $offset) = Pager::calculateLimitAndOffset();

        // Disregard the paging when rest parameters are given
        if (!empty($rest_parameters)) {
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

        if (!$columns) {
            // 500 error because this shouldn't happen in normal conditions
            // Columns are parsed upon adding a CSV resource and are always present
            \App::abort(500, "Cannot find the columns of the MySQL table file, this might be due to a corrupted database or a broken configuration.");
        }

        // Create aliases for the columns
        $aliases = $this->columnsRepo->getColumnAliases($source_definition['id'], 'MysqlDefinition');
        $pk = null;

        foreach ($columns as $column) {

            if (!empty($column['is_pk'])) {
                $pk = $column['column_name_alias'];
            }
        }

        // Connect to the database
        $db_config = array(
            'driver'    => 'mysql',
            'host'      => $source_definition['host'],
            'database'  => $source_definition['database'],
            'username'  => $source_definition['username'],
            'password'  => $source_definition['password'],
            'charset'   => 'utf8',
            'collation' => $source_definition['collation'],
        );

        // Configure a connection
        \Config::set('database.connections.mysqltmp', $db_config);

        // Make a database connection
        $db = \DB::connection('mysqltmp');

        // Check if a select statement has been given
        // If not, return all columns
        $select = \Request::get('select', null);

        if (empty($select)) {
            foreach ($columns as $column) {
                $select .= $column['column_name'] . ' AS ' . $column['column_name_alias'];
                $select .= ',';
            }

            $select = rtrim($select, ',');
        }

        $query_builder = $db->table($source_definition['datatable']);

        // Check for where statements in the query string
        $or_where = \Request::get('orWhere', null);

        $and_where = \Request::get('where', null);

        // Add the different where (and | or) statements
        if (!empty($and_where)) {
            if (is_array($and_where)) {
                foreach ($and_where as $stmt) {
                    $query_builder->whereRaw($stmt);
                }
            } else {
                $query_builder->whereRaw($and_where);
            }
        }

        if (!empty($or_where)) {
            if (is_array($or_where)) {
                foreach ($or_where as $stmt) {
                    $query_builder->orWhereRaw($stmt);
                }
            } else {
                $query_builder->orWhereRaw($or_where);
            }
        }

        try {

            // Get the total amount of records for the query for pagination purposes
            $total_rows = $query_builder->selectRaw($select)->skip($offset)->limit($limit)->count();

            $result = $query_builder->selectRaw($select)->skip($offset)->limit($limit)->get();
        } catch (QueryException $ex) {
            \App::abort(400, "A bad query has been made, make sure all passed statements are SQL friendly. The error message was: " . $ex->getMessage());
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

    public static function getParameters()
    {
        $parameters = array(
            'select' => array(
                'required' => false,
                'description' => 'A selection of columns, will be used as select statement in the mysql query. e.g. columnA, columnB'
            ),
            'where' => array(
                'required' => false,
                'description' => 'An and-where filter confirm to the MySQL specification. e.g. columnA > 1 In order to provide multiple filters use the [] notation e.g. where[]=id=1&where[]=name="Andy"'
            ),
            'orWhere' => array(
                'required' => false,
                'description' => 'An or-where filter confirm to the MySQL specification. e.g. columnA="this" In order to provide multiple filters use the [] notation e.g. orWhere[]=id=1&orWhere[]=name="Andy"'
            ),
        );

        return array_merge(parent::getParameters(), $parameters);
    }
}

<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface;

/**
 * Class that reads data from a MS SQL database
 * This requires pdo_dblib PHP extension.
 *
 * @author Jan Vansteenlandt
 * @license aGPLv3
 */
class SQLController extends ADataController
{
    public function __construct(TabularColumnsRepositoryInterface $tabular_columns)
    {
        $this->tabular_columns = $tabular_columns;
    }

    public function readData($source_definition, $rest_parameters = array())
    {
        if (\Config::get('app.debug')) {
            // Implode the configuration parameters
            $configuration = '';

            foreach ($source_definition as $key => $val) {
                $configuration .= $key . ' => ' . $val . ', ';
            }

            $configuration = rtrim($configuration, ', ');

            \Log::info('SqlController: Reading data for the resource with the following configuration: ' . $configuration);
        }

        list($limit, $offset) = Pager::calculateLimitAndOffset();

        // Fire the count query and retrieve the total amount of results
        $totalResultCount = $this->fireCountQuery($source_definition);

        // Get the configured columns
        $columns = $this->tabular_columns->getColumns($source_definition['id'], 'SqlDefinition');

        // Fire the data query
        $query = $this->fireQuery($source_definition);

        $results = array();

        if (!empty($query)) {
            $query->setFetchMode(\PDO::FETCH_OBJ);

            while ($row = $query->fetch()) {
                $row = (array) $row;

                $row = $this->createValues($columns, array_values($row));

                array_push($results, $row);
            }
        }

        // Calculate the paging headers
        $paging = Pager::calculatePagingHeaders($limit, $offset, $totalResultCount);

        $data = new Data();

        $data->data = $results;
        $data->preferred_formats = $this->getPreferredFormats();
        $data->paging = $paging;

        return $data;
    }

    /**
     * Create and return a PDO connection based on the configuration
     *
     * Aborts when the connection could not be made.
     *
     * @param array $config
     *
     * @return PDO
     */
    protected function getPDOConnection($config)
    {
        // Create the connection string
        $connectionString = 'dblib:host=' . $config['host'] . ':' . $config['port'];
        $connectionString .= ';dbname=' . $config['database'];

        if (\Config::get('app.debug')) {
            \Log::info('The connectionstring is: ' . $connectionString);
        }

        // Create and return the PDO object
        try {
            $pdo = new \PDO($connectionString, $config['username'], $config['password']);
        } catch (\PDOException $ex) {
            \App::abort(
                '400',
                'The connection could not be made, make sure all of the database variables are correct.
                The message we got is ' . $ex->getMessage()
            );
        }

        return $pdo;
    }

    /**
     * Create the PDO connection and execute the query
     *
     * @param array $config
     *
     * @return PDOStatement
     */
    protected function fireQuery($config)
    {
        $pdo = $this->getPDOConnection($config);

        // Calculate the paging headers
        list($limit, $offset) = Pager::calculateLimitAndOffset();

        // Fetch the query and replace the parameters in it
        $query = $config['query'];

        // Add the paging statements to the query
        $query .= ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $limit . ' ROWS ONLY';

        if (\Config::get('app.debug')) {
            \Log::info('The query that will be executed is: ' . $query);
        }

        $pdoStatement = $pdo->query($query);

        if (!$pdoStatement) {
            \Log::error("The query ( " . $query . " ) did not return any results.");

            return null;
        }

        return $pdoStatement;
    }

    /**
     * Create a count query, execute it and return the result
     *
     * @param array $config
     *
     * @return int
     */
    protected function fireCountQuery($config)
    {
        // Fetch the PDO connection
        $pdo = $this->getPDOConnection($config);

        // Throw away the select criteria and replace it with a count
        $query = $config['query'];

        preg_match('/.*?(from.*?)order\sby.*/smix', $query, $matches);

        // If no match has been found, the query must be malformed or the regex
        // needs to be tweaked.
        if (empty($matches[1])) {
            \App::abort('500', "Could not create a count query.");
        }

        // Get the query without the select statement
        $strippedQuery = $matches[1];

        $countQuery = "select count(*) as 'count' " . $strippedQuery;

        // Add the where filter if applicable
        $where = \Input::get('where', null);

        if (!empty($where)) {
            // Check if the statement contains a semi-colon, if so check for drop, update, insert, alter, create

            if (preg_match('/.*;[DROP|CREATE|INSERT|UPDATE|ALTER|DELETE].*/', $where)) {
                \Log::warning('A possible sql injection has been discovered: ' . $where);
            } else {
                $countQuery .= ' AND ' . $where;
            }
        }

        if (\Config::get('app.debug')) {
            \Log::info('Firing the count query, which is: '. $countQuery);
        }

        $pdoStatement = $pdo->query($countQuery);

        if (!empty($pdoStatement)) {
            $pdoStatement->setFetchMode(\PDO::FETCH_OBJ);

            $result = $pdoStatement->fetch();

            \Log::info('The total count is: ' . $result->count);

            return $result->count;
        } else {
            $errorInfo = implode(' -- ', $pdo->errorInfo());

            \App::abort('500', "Failed to execute the count query. The error information we have is: " . $errorInfo);
        }
    }

    /**
     * Replace the column names with aliases in the data
     *
     * @param array $columns The column configuration
     * @param array $data    The data
     *
     * @return array
     */
    protected function createValues($columns, $data)
    {
        $result = array();

        foreach ($columns as $column) {
            if (!empty($data[$column['index']]) || is_numeric(@$data[$column['index']])) {
                $result[$column['column_name_alias']] = \ForceUTF8\Encoding::fixUTF8(@$data[$column['index']]);
            } else {
                $index = $column['index'];

                $result[$column['column_name_alias']] = '';
            }
        }

        return $result;
    }

    public static function getParameters()
    {
        return parent::getParameters();
    }

    /**
     * Get the columns from the db query
     *
     * @param array $config
     *
     * @return array
     */
    public function parseColumns($config)
    {
        $pdo = $this->getPDOConnection($config);

        // Fetch the info about the columns we got from the request
        $columnsInfo = @$config['columns'];

        $aliases = array();

        // Map the configuration of the column onto the index of the column
        if (!empty($columnsInfo)) {
            foreach ($columnsInfo as $columnInfo) {
                $aliases[$columnInfo['index']] = array(
                    'alias' => $columnInfo['column_name_alias'],
                    'column_name' => $columnInfo['column_name'],
                );
            }
        }

        // Execute a query with a limit of 1
        // Calculate the paging headers
        $offset = 0;
        $limit = 1;

        // Fetch the query and replace the parameters in it
        $query = $config['query'];

        // Add the paging statements to the query
        $query .= ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $limit . ' ROWS ONLY';

        if (\Config::get('app.debug')) {
            \Log::info("Firing query to retrieve the column information: " . $query);
        }

        $pdoStatement = $pdo->query($query);

        if (!$pdoStatement) {
            \Log::error("The query ( " . $query . " ) did not return any results.");

            \App::abort('400', 'Failed to retrieve columns for the query.');
        }

        // Parse and return the columns
        $columns = array();

        $pdoStatement->setFetchMode(\PDO::FETCH_OBJ);

        $row = @$pdoStatement->fetch();

        if (!empty($pdoStatement) && !empty($row)) {
            $row = (array) $row;

            if (\Config::get('app.debug')) {
                $debug_row = implode(', ', array_keys($row));

                \Log::info('The column keys we got are: ' . $debug_row);
            }

            $columnNames = array_keys($row);

            $debug_row = implode(', ', array_values($columnNames));

            \Log::info('The column values we got are: ' . $debug_row);

            // Iterate every retrieved column from the query, only keep track
            // of the ones that were passed with the configuration
            foreach ($columnNames as $index => $column_name) {
                array_push(
                    $columns,
                    array(
                        'index' => $index,
                        'column_name' => $column_name,
                        'column_name_alias' => $column_name,
                    )
                );
            }
        } else {
            \App::abort(400, "The PDO statement was empty, probably the query failed.");
        }

        \Log::info("columns from controller" . json_encode($columns));

        return $columns;
    }
}

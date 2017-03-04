<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\MysqlDefinitionRepositoryInterface;

class MysqlDefinitionRepository extends TabularBaseRepository implements MysqlDefinitionRepositoryInterface
{
    protected $rules = array(
        'host' => 'required',
        'port' => 'integer',
        'database' => 'required',
        'username' => 'required',
        'query' => 'required|mysqlquery',
        'description' => 'required'
    );

    public function __construct(\MysqlDefinition $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Retrieve the set of create parameters that make up a MySQL definition.
     * Include the parameters that make up relationships with this model.
     */
    public function getAllParameters()
    {
        $column_params = array(
            'columns' =>
                array(
                    'description' => 'Columns must be an array of objects of which the template is described in the parameters section.',
                    'parameters' => $this->tabular_repository->getCreateParameters(),
                )
        );

        $geo_params = array(
            'geo' =>
                array(
                    'description' => 'Geo must be an array of objects of which the template is described in the parameters section.',
                    'parameters' => $this->geo_repository->getCreateParameters(),
                )
        );

        return array_merge($this->getCreateParameters(), $column_params, $geo_params);
    }

    protected function extractColumns($input)
    {
        try {
            $db_config = array(
                'driver'    => 'mysql',
                'host'      => $input['mysql_host'],
                'database'  => $input['database'],
                'username'  => $input['mysql_username'],
                'password'  => $input['mysql_password'],
                'charset'   => 'utf8',
                'collation' => $input['collation'],
                'port' => $input['mysql_port'],
                );

            // Configure a connection
            \Config::set('database.connections.testconnection', $db_config);

            // Make a database connection
            $db = \DB::connection('testconnection');

            // Get the schema builder of the database connection
            $schema = $db->getSchemaBuilder();
            $connection = $schema->getConnection();
            $result = $connection->selectOne($input['query']);

            if (empty($result)) {
                \App::abort(400, 'The query did not return any results.');
            }

            $db_columns = array_keys((array)$result);

            $columns_info = @$config['columns'];
            $pk = @$config['pk'];

            // Prepare the aliases
            $aliases = array();

            if (! empty($columns_info)) {
                foreach ($columns_info as $column_info) {
                    $aliases[$column_info['index']] = $column_info['column_name_alias'];
                }
            }

            // Create the columns array
            $columns = array();

            foreach ($db_columns as $index => $column) {
                array_push($columns, array(
                    'index' => $index,
                    'column_name' => $column,
                    'column_name_alias' => empty($aliases[$index]) ? $column : $aliases[$index],
                    'pk' => ($pk === $index)
                ));
            }

            return $columns;
        } catch (\Exception $ex) {
            \Log::error('Something went wrong while extracting columns from the query results, or the query itself');
            \Log::error($ex->getMessage());

            throw new \Exception('Something went wrong while connecting to the database, make sure the application can reach the database with the given credentials and that the query is valid. The technical error we got was: ' . $ex->getMessage());
        }
    }

    /**
     * Return the properties (= column fields) for this model.
     */
    public function getCreateParameters()
    {
        return array(
            'mysql_host' => array(
                'required' => true,
                'name' => 'Host',
                'description' => 'The host of the MySQL database.',
                'type' => 'string',
            ),
            'mysql_port' => array(
                'required' => false,
                'name' => 'Port',
                'description' => 'The port of the MySQL database where a connection can be made to.',
                'type' => 'string',
                'default_value' => 3306
            ),
            'database' => array(
                'required' => true,
                'name' => 'Database',
                'description' => 'The name of the database where the datatable, that needs to be published, resides.',
                'type' => 'string',
            ),
            'mysql_username' => array(
                'required' => true,
                'name' => 'Username',
                'description' => 'A username that has read permissions on the provided datatable. Safety first, make sure the user only has read permissions.',
                'type' => 'string',
            ),
            'mysql_password' => array(
                'required' => false,
                'name' => 'Password',
                'description' => 'The password for the user that has read permissions.',
                'default_value' => '',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'text',
            ),
            'collation' => array(
                'required' => false,
                'name' => 'Collation',
                'description' => 'The collation of the datatable.',
                'default_value' => 'utf8_unicode_ci',
                'type' => 'string',
            ),
            'pk' => array(
                'required' => false,
                'name' => 'Primary key',
                'description' => 'This is a shortcut to define a primary key of this dataset.
                                The value must be the index of the column you want each row to be mapped on.
                                The pk property will never explicitly appear in the definition, but will manifest itself as part of a column property.',
                'type' => 'integer',
            ),
            'query' => array(
                'required' => true,
                'name' => 'Query',
                'description' => 'The query of which the results will be published as open data.',
                'type' => 'text'
            )
        );
    }
}

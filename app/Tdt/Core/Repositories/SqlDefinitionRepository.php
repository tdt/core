<?php namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\SqlDefinitionRepositoryInterface as RepositoryInterface;

class SqlDefinitionRepository extends TabularBaseRepository implements RepositoryInterface
{
    protected $rules = array(
        'port' => 'integer|required',
        'host' => 'required',
        'username' => 'required',
        'password' => 'required',
        'database' => 'required',
        'query' => 'required',
        );

    public function __construct(\SqlDefinition $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Return an array of create parameters with info attached
     * e.g. array( 'create_parameter' => array(
     *              'required' => true,
     *              'description' => '...',
     *              'type' => 'string',
     *              'name' => 'pretty name'
     *       ), ...)
     *
     * @return array
     */
    public function getCreateParameters()
    {
        return array(
            'host' => array(
                'required' => true,
                'name' => 'Host',
                'description' => 'The IP adress of the SQL Server',
                'type' => 'string',
                'default_value' => \Config::get('database.connections.sqlsrv.host'),
                ),
            'username' => array(
                'required' => true,
                'name' => 'Username',
                'description' => 'The username to connect to the SQL server',
                'type' => 'string',
                'default_value' => \Config::get('database.connections.sqlsrv.username'),
                ),
            'password' => array(
                'required' => true,
                'name' => 'Password',
                'description' => 'The password of the user.',
                'type' => 'string',
                'default_value' => \Config::get('database.connections.sqlsrv.password'),
                ),
            'database' => array(
                'required' => true,
                'name' => 'Database',
                'description' => 'The name of the database to connect to.',
                'type' => 'string',
                'default_value' => \Config::get('database.connections.sqlsrv.database'),
                ),
            'port' => array(
                'required' => true,
                'name' => 'Port',
                'description' => 'The port to connect to on the server.',
                'type' => 'string',
                'default_value' => \Config::get('database.connections.sqlsrv.port'),
                ),
            'query' => array(
                'required' => true,
                'name' => 'Query',
                'description' => 'The query to be executed on the SQL server.',
                'type' => 'text',
                )
        );
    }

    public function update($tabular_id, array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->patchInput($tabular_id, $input);

        $model_definition = $this->getById($tabular_id);

        $model_name = $this->getModelName();

        // Validate the column properties (perhaps we need to put this extraction somewhere else)
        $extracted_columns = $this->extractColumns($input);

        $input_columns = @$input['columns'];

        if (empty($input_columns)) {
            $input_columns = array();
        }

        $columns = $this->tabular_repository->validateBulk($extracted_columns, $input_columns);

        // Validate the geo properties by comparing them to what
        // properties we extract, to which has been provided by the input
        $extracted_geo = $this->extractGeoProperties($input, $columns);

        $geo = array();

        if (!empty($input['geo'])) {
            $geo = $input['geo'];
        }

        $geo = $this->geo_repository->validateBulk($extracted_geo, $geo);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys($this->getCreateParameters()));

        $productModel = $this->model->find($tabular_id);
        $productModel->update($input);

        // All has been validated, let's replace the current meta-data
        $this->tabular_repository->deleteBulk($tabular_id, $model_name);
        $this->geo_repository->deleteBulk($tabular_id, $model_name);

        // Check for a primary key, and add it to the columns
        $pk = @$input['pk'];

        if (!is_null($pk) && is_numeric($pk) && $pk >= 0 && $pk < count($columns)) {
            $columns[$pk]['is_pk'] = 1;
        }

        // Store the columns and geo meta-data
        $this->tabular_repository->storeBulk($tabular_id, $model_name, $columns);

        if (!empty($geo)) {
            $this->geo_repository->storeBulk($tabular_id, $model_name, $geo);
        }

        return $productModel->toArray();
    }

    /**
     * Extract and return an array of columns from the tabular source:
     *
     *   column: index, is_pk, column_name, column_name_alias
     *
     * @param array $input
     *
     * @return array columns
     */
    protected function extractColumns($input)
    {
        $controller = \App::make('Tdt\Core\DataControllers\SQLController');

        if (!empty($input['id'])) {
            $model = $this->model->find(@$input['id']);

            $columns = $this->tabular_repository->getColumns($input['id'], 'SqlDefinition');

            $input['columns'] = $columns;
        }

        return $controller->parseColumns($input);
    }

    /**
     * Return an array of all the create parameters, also the parameters
     * that are necessary for further internal relationships
     *
     * (e.g. CSV needs columns, in an RDBMS as back-end this results in model relationships)
     *
     * @return array
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

        return array_merge($this->getCreateParameters(), $column_params);
    }
}

<?php

namespace repositories;

use repositories\interfaces\XlsDefinitionRepositoryInterface;

use tdt\core\datacontrollers\XLSController;

class XlsDefinitionRepository extends TabularBaseRepository implements XlsDefinitionRepositoryInterface
{

    protected $rules = array(
        'has_header_row' => 'integer|min:0|max:1',
        'start_row' => 'integer',
        'uri' => 'uri|required',
        'description' => 'required',
    );

    public function __construct(\XlsDefinition $model)
    {

        parent::__construct();

        $this->model = $model;
    }

    public function store($input)
    {

        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        // Validate the column properties (perhaps we need to put this extraction somewhere else)
        $extracted_columns = XLSController::parseColumns($input);

        $columns = $this->tabular_repository->validate($extracted_columns, @$input['columns']);

        // Validate the geo properties and take into consideration the alias for the column that the geo property might have
        $geo = @$input['geo'];

        if (empty($geo)) {
            $geo = array();
        }else{
            $geo = $this->geo_repository->validate(@$input['geo']);
        }

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys($this->getCreateParameters()));
        $xls_definition = \XlsDefinition::create($input);

        // Store the columns and geo meta-data
        $this->tabular_repository->storeBulk($xls_definition->id, 'XlsDefinition', $columns);

        if(!empty($geo))
            $this->geo_repository->storeBulk($xls_definition->id, 'XlsDefinition', $geo);

        return $xls_definition->toArray();
    }

    public function update($id, $input)
    {

        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        $xls_definition = $this->getById($id);

        // Validate the column properties (perhaps we need to put this extraction somewhere else)
        $extracted_columns = XLSController::parseColumns($xls_definition->toArray());

        $columns = $this->tabular_repository->validate($extracted_columns, @$input['columns']);

        // Validate the geo properties and take into consideration the alias for the column that the geo property might have
        $geo = $this->geo_repository->validate(@$input['geo']);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys($this->getCreateParameters()));

        $xls_def_object = $this->model->find($id);
        $xls_def_object->update($input);

        // All has been validated, let's replace the current meta-data
        $this->tabular_repository->deleteBulk($id);
        $this->geo_repository->deleteBulk($id);

        // Store the columns and geo meta-data
        $this->tabular_repository->storeBulk($id, 'CsvDefinition', $columns);

        if(!empty($geo))
            $this->geo_repository->storeBulk($id, 'CsvDefinition', $geo);

        return $xls_def_object->toArray();
    }

    /**
     * Retrieve the set of create parameters that make up a XLS definition.
     */
    public function getCreateParameters()
    {

        return array(
                'uri' => array(
                    'required' => true,
                    'name' => 'URI',
                    'description' => 'The location of the XLS file, either a URL or a local file location.',
                    'type' => 'string',
                ),
                'description' => array(
                    'required' => true,
                    'name' => 'Description',
                    'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                    'type' => 'string',
                ),
                'sheet' => array(
                    'required' => false,
                    'name' => 'XLS sheet',
                    'description' => 'The sheet name in which the tabular data resides.',
                    'default_value' => ',',
                    'type' => 'string',
                ),
                'has_header_row' => array(
                    'required' => false,
                    'name' => 'Header row',
                    'description' => 'Boolean parameter defining if the XLS file contains a header row that contains the column names.',
                    'default_value' => 1,
                    'type' => 'boolean',
                ),
                'start_row' => array(
                    'required' => false,
                    'name' => 'Start row',
                    'description' => 'Defines the row at which the data (and header row if present) starts in the file.',
                    'default_value' => 0,
                    'type' => 'integer',
                ),
                'pk' => array(
                    'required' => false,
                    'name' => 'Primary key',
                    'description' => 'This is a shortcut to define a primary key of this dataset. The value must be the index of the column you want each row to be mapped on. The pk property will never explicitly appear in the definition, but will manifest itself as part of a column property.',
                    'type' => 'integer',
                ),
        );
    }

    /**
     * Retrieve the set of create parameters that make up a XLS definition.
     * Include the parameters that make up relationships with this model.
     */
    public function getAllParameters()
    {

         $column_params = array(
            'columns' =>
                array('description' => 'Columns must be an array of objects of which the template is described in the parameters section.',
                  'parameters' => $this->tabular_repository->getCreateParameters(),
            ),
        );

        return array_merge($this->getCreateParameters(), $column_params);
    }
}
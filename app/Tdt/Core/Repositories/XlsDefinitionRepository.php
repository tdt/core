<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\XlsDefinitionRepositoryInterface;

use Tdt\Core\DataControllers\XLSController;

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

    protected function extractColumns($input)
    {
        return XLSController::parseColumns($input);
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
                'default_value' => 'Sheet1',
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

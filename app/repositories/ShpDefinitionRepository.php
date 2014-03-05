<?php

namespace repositories;

use repositories\interfaces\ShpDefinitionRepositoryInterface;
use tdt\core\datacontrollers\SHPController;

class ShpDefinitionRepository extends TabularBaseRepository implements ShpDefinitionRepositoryInterface{

    protected $rules = array(
        'uri' => 'required|uri',
        'description' => 'required',
    );

    public function __construct(\ShpDefinition $model){

        parent::__construct();

        $this->model = $model;
    }

    public function store(array $input){

        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        // Validate the column properties (perhaps we need to put this extraction somewhere else)
        $extracted_columns = SHPController::parseColumns($input);

        $columns = $this->tabular_repository->validate($extracted_columns, @$input['columns']);

        // Validate the geo properties and take into consideration the alias for the column that the geo property might have
        $geo = SHPController::parseGeoProperty($input, $columns);

        $this->geo_repository->validate($geo);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys($this->getCreateParameters()));
        $shp_definition = \ShpDefinition::create($input);

        // Store the columns and geo meta-data
        $this->tabular_repository->storeBulk($shp_definition->id, 'ShpDefinition', $columns);

        if(!empty($geo))
            $this->geo_repository->storeBulk($shp_definition->id, 'ShpDefinition', $geo);

        return $shp_definition->toArray();
    }


    public function update($id, array $input){

        // Process input (e.g. set default values to empty properties)
        $input = $this->patchInput($id, $input);

        $shp_definition = $this->getById($id);

        // Validate the column properties (perhaps we need to put this extraction somewhere else)
        $extracted_columns = SHPController::parseColumns($shp_definition);

        $input_columns = @$input['columns'];

        if(empty($input_columns))
            $input_columns = array();

        $columns = $this->tabular_repository->validate($extracted_columns, $input_columns);

        // Validate the geo properties and take into consideration the alias for the column that the geo property might have
        $geo = SHPController::parseGeoProperty($input, $columns);
        $geo = $this->geo_repository->validate($geo);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys($this->getCreateParameters()));

        $shp_def_object = $this->model->find($id);
        $shp_def_object->update($input);

        // All has been validated, let's replace the current meta-data
        $this->tabular_repository->deleteBulk($id, 'ShpDefinition');
        $this->geo_repository->deleteBulk($id, 'ShpDefinition');

        // Store the columns and geo meta-data
        $this->tabular_repository->storeBulk($id, 'ShpDefinition', $columns);

        if(!empty($geo))
            $this->geo_repository->storeBulk($id, 'ShpDefinition', $geo);

        return $shp_def_object->toArray();
    }

    /**
     * Retrieve the set of create parameters that make up a SHP definition.
     */
    public function getCreateParameters(){
        return array(
                'uri' => array(
                    'required' => true,
                    'name' => 'URI',
                    'description' => 'The location of the SHP file, either a URL or a local file location.',
                    'type' => 'string',
                ),
                'description' => array(
                    'required' => true,
                    'name' => 'Description',
                    'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                    'type' => 'string',
                ),
                'epsg' => array(
                    'required' => false,
                    'name' => 'EPSG code',
                    'description' => 'This parameter holds the EPSG code in which the geometric properties in the shape file are encoded.',
                    'default_value' => 4326,
                    'type' => 'string',
                )
            );
    }

    /**
     * Retrieve the set of create parameters that make up a CSV definition.
     * Include the parameters that make up relationships with this model.
     */
    public function getAllParameters(){

         $column_params = array('columns' => array('description' => 'Columns must be an array of objects of which the template is described in the parameters section.',
                                                'parameters' => $this->tabular_repository->getCreateParameters(),
                                            )
        );

        return array_merge($this->getCreateParameters(), $column_params);
    }
}
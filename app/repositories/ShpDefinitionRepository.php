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

    protected function extractGeoProperties($input, $columns){
        return SHPController::parseGeoProperty($input, $columns);
    }

    protected function extractColumns($input){
        return SHPController::parseColumns($input);
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
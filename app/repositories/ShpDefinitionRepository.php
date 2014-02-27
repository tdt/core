<?php

namespace repositories;

use repositories\interfaces\ShpDefinitionRepositoryInterface;
use tdt\core\datacontrollers\SHPController;

class ShpDefinitionRepository extends BaseRepository implements ShpDefinitionRepositoryInterface{

    protected $rules = array(
        'uri' => 'required|uri',
        'description' => 'required',
    );

    public function __construct(\ShpDefinition $model){
        $this->model = $model;
    }

    public function store($input){

        // Validate the column properties (perhaps we need to put this extraction somewhere else)
        $extracted_columns = SHPController::parseColumns($input);

        $tabular_repository = \App::make('repositories\interfaces\TabularColumnsRepositoryInterface');
        $columns = $tabular_repository->validate($extracted_columns, @$input['columns']);

        // Validate the geo properties and take into consideration the alias for the column that the geo property might have
        $geo = SHPController::parseGeoProperty($input, $columns);

        //dd($geo);
        $geo_repository = \App::make('repositories\interfaces\GeoPropertyRepositoryInterface');
        $geo_repository->validate($geo);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys(\ShpDefinition::getCreateParameters()));
        $shp_definition = \ShpDefinition::create($input);

        // Store the columns and geo meta-data
        $tabular_repository->storeBulk($shp_definition->id, 'ShpDefinition', $columns);

        if(!empty($geo))
            $geo_repository->storeBulk($shp_definition->id, 'ShpDefinition', $geo);

        return $shp_definition->toArray();
    }


    public function update($id, $input){

        $shp_definition = $this->getById($id);

        // Validate the column properties (perhaps we need to put this extraction somewhere else)
        $extracted_columns = SHPController::parseColumns($shp_definition->toArray());

        $tabular_repository = \App::make('repositories\interfaces\TabularColumnsRepositoryInterface');
        $columns = $tabular_repository->validate($extracted_columns, @$input['columns']);

        // Validate the geo properties and take into consideration the alias for the column that the geo property might have
        $geo = SHPController::parseGeoProperty($input, $columns);
        $geo_repository = \App::make('repositories\interfaces\GeoPropertyRepositoryInterface');
        $geo = $geo_repository->validate($geo);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys(\ShpDefinition::getCreateParameters()));
        $shp_definition->update($input);

        // All has been validated, let's replace the current meta-data
        $tabular_repository->deleteBulk($shp_definition->id);
        $geo_repository->deleteBulk($shp_definition->id);

        // Store the columns and geo meta-data
        $tabular_repository->storeBulk($shp_definition->id, 'ShpDefinition', $columns);

        if(!empty($geo))
            $geo_repository->storeBulk($shp_definition->id, 'ShpDefinition', $geo);

        return $shp_definition->toArray();
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
                                                'parameters' => TabularColumns::getCreateParameters(),
                                            )
        );

        return array_merge(self::getCreateParameters(), $column_params);
    }
}
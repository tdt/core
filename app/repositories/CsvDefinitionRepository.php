<?php

namespace repositories;

use repositories\interfaces\CsvDefinitionRepositoryInterface;
use tdt\core\datacontrollers\CSVController;

class CsvDefinitionRepository extends BaseRepository implements CsvDefinitionRepositoryInterface{

    protected $rules = array(
        'has_header_row' => 'integer|min:0|max:1',
        'start_row' => 'integer',
        'uri' => 'uri|required',
        'description' => 'required',
    );

    public function __construct(\CsvDefinition $model){
        $this->model = $model;
    }

    public function store($input){

        // Validate the column properties (perhaps we need to put this extraction somewhere else)
        $extracted_columns = CSVController::parseColumns($input);

        $tabular_repository = \App::make('repositories\interfaces\TabularColumnsRepositoryInterface');
        $columns = $tabular_repository->validate($extracted_columns, @$input['columns']);

        // Validate the geo properties and take into consideration the alias for the column that the geo property might have
        $geo_repository = \App::make('repositories\interfaces\GeoPropertyRepositoryInterface');


        if(empty($geo)){
            $geo = array();
        }else{
            $geo = $geo_repository->validate(@$input['geo']);
        }

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys(\CsvDefinition::getCreateParameters()));
        $csv_definition = \CsvDefinition::create($input);

        // Store the columns and geo meta-data
        $tabular_repository->storeBulk($csv_definition->id, 'CsvDefinition', $columns);

        if(!empty($geo))
            $geo_repository->storeBulk($csv_definition->id, 'CsvDefinition', $geo);

        return $csv_definition->toArray();
    }


    public function update($id, $input){

        $csv_definition = $this->getById($id);

        // Validate the column properties (perhaps we need to put this extraction somewhere else)
        $extracted_columns = CSVController::parseColumns($csv_definition->toArray());

        $tabular_repository = \App::make('repositories\interfaces\TabularColumnsRepositoryInterface');
        $columns = $tabular_repository->validate($extracted_columns, @$input['columns']);

        // Validate the geo properties and take into consideration the alias for the column that the geo property might have
        $geo_repository = \App::make('repositories\interfaces\GeoPropertyRepositoryInterface');
        $geo = $geo_repository->validate(@$input['geo']);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys(\CsvDefinition::getCreateParameters()));
        $csv_definition->update($input);

        // All has been validated, let's replace the current meta-data
        $tabular_repository->deleteBulk($csv_definition->id);
        $geo_repository->deleteBulk($csv_definition->id);

        // Store the columns and geo meta-data
        $tabular_repository->storeBulk($csv_definition->id, 'CsvDefinition', $columns);

        if(!empty($geo))
            $geo_repository->storeBulk($csv_definition->id, 'CsvDefinition', $geo);

        return $csv_definition->toArray();
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

        $geo_params = array('geo' => array('description' => 'Geo must be an array of objects of which the template is described in the parameters section.',
                                            'parameters' => GeoProperty::getCreateParameters(),
        ));

        return array_merge(self::getCreateParameters(), $column_params, $geo_params);
    }

    /**
     * Return the properties ( = column fields ) for this model.
     */
    public function getCreateParameters(){
        return array(
                'uri' => array(
                    'required' => true,
                    'name' => 'URI',
                    'description' => 'The location of the CSV file, either a URL or a local file location.',
                    'type' => 'string',
                ),
                'description' => array(
                    'required' => true,
                    'name' => 'Description',
                    'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                    'type' => 'string',
                ),
                'delimiter' => array(
                    'required' => false,
                    'name' => 'Delimiter',
                    'description' => 'The delimiter of the separated value file.',
                    'default_value' => ',',
                    'type' => 'string',
                ),
                'has_header_row' => array(
                    'required' => false,
                    'name' => 'Header row',
                    'description' => 'Boolean parameter defining if the separated value file contains a header row that contains the column names.',
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
}
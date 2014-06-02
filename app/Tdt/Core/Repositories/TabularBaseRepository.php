<?php

namespace Tdt\Core\Repositories;

abstract class TabularBaseRepository extends BaseDefinitionRepository
{

    protected $tabular_repository;
    protected $geo_repository;

    public function __construct()
    {
        $this->tabular_repository = \App::make('Tdt\\Core\\Repositories\\Interfaces\\TabularColumnsRepositoryInterface');
        $this->geo_repository = \App::make('Tdt\\Core\\Repositories\\Interfaces\\GeoPropertyRepositoryInterface');
    }

    public function store(array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        // Validate the column properties by comparing them to what
        // columns we extract, to which has been provided by the input
        $extracted_columns = $this->extractColumns($input);

        $columns = array();

        if (!empty($input['columns'])) {
            $columns = $input['columns'];
        }

        $columns = $this->tabular_repository->validateBulk($extracted_columns, $columns);

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

        // Unset the pk property, only used for easy pk configuration of a column
        // is not included in the tabular model itself, but part of the tabularcolumns table
        unset($input['pk']);

        $tabular_definition = $this->model->create($input);

        $model_name = $this->getModelName();

        // Store the columns and optional geo meta-data
        $this->tabular_repository->storeBulk($tabular_definition->id, $model_name, $columns);

        if (!empty($geo)) {
            $this->geo_repository->storeBulk($tabular_definition->id, $model_name, $geo);
        }

        return $tabular_definition->toArray();
    }

    public function update($tabular_id, array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->patchInput($tabular_id, $input);

        $model_definition = $this->getById($tabular_id);

        $model_name = $this->getModelName();

        // Validate the column properties (perhaps we need to put this extraction somewhere else)
        $extracted_columns = $this->extractColumns($model_definition);

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

        $csv_def_object = $this->model->find($tabular_id);
        $csv_def_object->update($input);

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

        return $csv_def_object->toArray();
    }

    private function getModelName()
    {
        // This only works if you follow the naming conventions for source types and their repositories
        // If you want to extend, please take this as a guideline
        return get_class($this->model);
    }

    /**
     * Extract and return an array of columns from the tabular source:
     *
     *   column: index, is_pk, column_name, column_name_alias
     *
     * @param array $input
     * @return array columns
     */
    abstract protected function extractColumns($input);

    /**
     * Process the columns and return geo properties
     *
     * @param array $input
     * @param array $columns
     * @return array geo properties
     */
    protected function extractGeoProperties($input, $columns)
    {
        return array();
    }
}

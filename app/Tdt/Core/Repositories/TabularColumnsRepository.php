<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\TabularColumnsRepositoryInterface;

class TabularColumnsRepository extends BaseDefinitionRepository implements TabularColumnsRepositoryInterface
{

    protected $rules = array(
        'pk' => 'integer',
        'index' => 'integer|min:0|required',
        'column_name' => 'required',
        'column_name_alias' => 'required',
    );

    public function __construct(\TabularColumns $model)
    {
        $this->model = $model;
    }

    public function store(array $input)
    {
        return \TabularColumns::create($input);
    }

    /**
     * Validate a set of columns and check for mismatches
     * between the given set of columns and the extracted ones
     */
    public function validateBulk(array $extracted_columns, array $provided_columns)
    {
        // If columns are provided, check if they exist and have the correct index
        if (!empty($provided_columns)) {

            // Validate the provided columns
            foreach ($provided_columns as $provided_column) {
                $this->validate($provided_column);
            }

            $tmp = array();

            // Index the column objects on the column name
            foreach ($provided_columns as $column) {
                $tmp[$column['column_name']] = $column;
            }

            $tmp_columns = array();

            foreach ($extracted_columns as $column) {
                $tmp_columns[$column['column_name']] = $column;
            }

            // If the column name of a provided column doesn't exist, or an index doesn't match, abort
            foreach ($tmp as $column_name => $column) {

                $tmp_column = $tmp_columns[$column_name];

                if (empty($tmp_column)) {
                    \App::abort(404, "The column name ($column_name) was not found in the tabular datasource.");
                }

                if ($tmp_column['index'] != $column['index']) {
                    \App::abort(400, "The column name ($column_name) was found, but the index isn't correct.");
                }
            }

            $extracted_columns = $provided_columns;
        }

        return $extracted_columns;
    }

    public function getColumnAliases($tabular_id, $type)
    {

        $tabular_columns = \TabularColumns::where('tabular_id', '=', $tabular_id)->where('tabular_type', '=', $type, 'AND')->get()->toArray();

        $columns = array();

        foreach ($tabular_columns as $column) {
            $columns[$column['column_name']] = $column['column_name_alias'];
        }

        return $columns;
    }

    public function getColumns($tabular_id, $type)
    {
        return \TabularColumns::where('tabular_id', '=', $tabular_id)->where('tabular_type', '=', $type, 'AND')->get()->toArray();
    }

    public function storeBulk($tabular_id, $type, $columns)
    {

        foreach ($columns as $column) {

            $column['tabular_id'] = $tabular_id;
            $column['tabular_type'] = $type;

            $this->store($column);
        }
    }

    /**
     * Delete all columns associated with the tabular_id ($tabular_id)
     */
    public function deleteBulk($tabular_id, $type)
    {

        $columns = \TabularColumns::where('tabular_id', '=', $tabular_id)->where('tabular_type', '=', $type, 'AND')->get();
        foreach ($columns as $column) {
            $column->delete();
        }
    }

    public function validate(array $input)
    {
        $validator = $this->getValidator($input);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, $message);
        }
    }

    /**
     * Retrieve the set of create parameters that make up a TabularColumn model.
     */
    public function getCreateParameters()
    {
        return array(
            'column_name' => array(
                'required' => false,
                'name' => 'Column name',
                'description' => 'The column name that corresponds with the index.',
                'type' => 'string',
            ),
            'pk' => array(
                'required' => false,
                'name' => 'Primary key',
                'description' => 'The index of the column that serves as a primary key when data is published. Rows will thus be indexed onto the value of the column which index is represented by the pk value.',
                'type' => 'integer',
            ),
            'index' => array(
                'required' => false,
                'name' => 'Index',
                'description' => 'The index of the column, starting from 0.',
                'type' => 'integer',
            ),
            'column_name_alias' => array(
                'required' => false,
                'name' => 'Column name alias',
                'description' => 'Provides an alias for the column name and will be used when data is requested instead of the column_name property.',
                'type' => 'string',
            ),
        );
    }
}

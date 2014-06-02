<?php

namespace Tdt\Core\Repositories;

use Illuminate\Support\Facades\Validator;

/**
 * BaseDefinitionRepository implements most of the interface that DefinitionRepositories use
 * making new ones will result in ever so little work
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class BaseDefinitionRepository
{

    protected $model;

    protected $error_messages = array(
        'uri' => "The uri provided could not be resolved.",
        'file' => 'The uri provided could not be resolved, if the uri is a system path try putting file:// in front of it.',
        'json' => 'The contents of the uri could not be parsed as JSON, make sure the JSON is valid.',
        'sparqlquery' => "The query could not be validated, make sure you don't use a limit and/or offset statement and that a select or construct statement is present.",
        'collectionuri' => "The collection uri cannot start with preserved uri namespaces e.g. discovery, api, ...",
    );

    public function getValidator(array $input)
    {
        return Validator::make($input, $this->rules, $this->error_messages);
    }

    public function getAll()
    {
        return $this->model->all()->toArray();
    }

    public function getCreateParameters()
    {
        return array();
    }

    public function getAllParameters()
    {
        return $this->getCreateParameters();
    }

    public function store(array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        return $this->model->create(array_only($input, array_keys($this->getCreateParameters())));
    }

    public function getById($model_id)
    {
        $model = $this->model->find($model_id);

        if (!empty($model)) {
            return $model->toArray();
        }

        return $model;
    }

    public function delete($model_id)
    {
        $model = $this->model->find($model_id);

        if (!empty($model)) {
            return $model->delete();
        }
    }

    public function update($model_id, array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        $model_object = $this->model->find($model_id);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys($this->getCreateParameters()));
        $model_object->update($input);

        return $model_object->toArray();
    }

    /**
     * Pre-process the input by assigning default values for empty properties
     */
    protected function processInput(array $input)
    {
        foreach ($this->getCreateParameters() as $key => $info) {

            if (empty($input[$key]) && (!empty($info['default_value']) || !is_null(@$info['default_value']))) {
                $input[$key] = @$info['default_value'];
            } elseif (empty($input[$key])) {
                $input[$key] = null;
            }
        }

        return $input;
    }

    /**
     * Patch the input given with the existing properties of a model and return the resulting array
     *
     * @param integer $id
     * @param array $input
     * @return array model
     */
    protected function patchInput($model_id, array $input)
    {
        $model = $this->getById($model_id);

        foreach ($model as $property => $value) {

            if (empty($input[$property]) && !is_numeric(@$input[$property])) {
                $input[$property] = $model[$property];
            }
        }

        return $input;
    }
}

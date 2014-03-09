<?php

namespace repositories;

use repositories\interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class BaseRepository implements BaseRepositoryInterface
{

    protected $model;

    protected $error_messages = array(
            'uri' => "The uri provided could not be resolved.",
            'file' => 'The uri provided could not be resolved, if the uri is a system path try putting file:// in front of it.',
            'json' => 'The contents of the uri could not be parsed as JSON, make sure the JSON is valid.',
            'sparqlquery' => "The query could not be validated, make sure you don't use a limit and/or offset statement and that a select or construct statement is present.",
            'collectionuri' => "The collection uri cannot start with preserved uri namespaces e.g. discovery, api, ...",
    );

    public function getValidator($input){
        return Validator::make($input, $this->rules, $this->error_messages);
    }

    public function getCreateParameters(){
        return array();
    }

    public function getAllParameters(){
        return $this->getCreateParameters();
    }

    public function store($input){

        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        return $this->model->create(array_only($input, array_keys($this->getCreateParameters())));
    }

    public function getById($id){

        $model = $this->model->find($id);

        if(!empty($model))
            return $model->toArray();

        return $model;
    }

    public function delete($id){

        $model = $this->model->find($id);

        if(!empty($model))
            return $model->delete();
    }

    public function update($id, $input){

        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        $model_object = $this->model->find($id);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys($this->getCreateParameters()));
        $model_object->update($input);

        return $model_object->toArray();
    }

    /**
     * Pre-process the input by assigning default values for empty properties
     */
    protected function processInput($input){

        foreach($this->getCreateParameters() as $key => $info){

            if(empty($input[$key]) && !empty($info['default_value']) || is_numeric(@$info['default_value'])){
                $input[$key] = @$info['default_value'];
            }else if(empty($input[$key])){
                $input[$key] = null;
            }
        }

        return $input;
    }

}
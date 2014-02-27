<?php

namespace repositories;

use repositories\interfaces\BaseRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class BaseRepository implements BaseRepositoryInterface{

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

        return $this->model->create(array_only($input, array_keys($this->getCreateParameters())));
    }


    public function getById($id){

        return $this->model->find($id);
    }

    public function delete($id){

        $model = $this->model->find($id);

        if(!empty($model))
            return $model->delete();
    }

    public function update($id, $input){

        $model = $this->getById($id);

        // Validation has been done, lets create the models
        $input = array_only($input, array_keys(self::getCreateParameters()));
        $model->update($input);
    }

}
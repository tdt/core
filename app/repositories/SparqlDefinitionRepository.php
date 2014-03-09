<?php

namespace repositories;

use repositories\interfaces\SparqlDefinitionRepositoryInterface;

class SparqlDefinitionRepository extends BaseRepository implements SparqlDefinitionRepositoryInterface
{

    protected $rules = array(
        'endpoint' => 'required',
        'query' => 'required|sparqlquery',
        'description' => 'required',
    );

    public function __construct(\SparqlDefinition $model){
        $this->model = $model;
    }

    /**
     * Retrieve the set of create parameters that make up a SPARQL definition.
     */
    public function getCreateParameters(){
        return array(
            'endpoint' => array(
                'required' => true,
                'name' => 'SPARQL endpoint',
                'description' => 'The uri of the SPARQL end-point (e.g. http://foobar:8890/sparql-auth).',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'string',
            ),
            'query' => array(
                'required' => true,
                'name' => 'SPARQL query',
                'description' =>  'The query to be executed.',
                'type' => 'text',
            ),
            'endpoint_user' => array(
                'required' => false,
                'name' => 'SPARQL endpoint user',
                'description' => 'Username of the user that has sufficient rights to query the sparql endpoint.',
                'type' => 'string',
            ),
            'endpoint_password' => array(
                'required' => false,
                'name' => "SPARQL endpoint user's password",
                'description' => 'Password of the provided user to query a sparql endpoint.',
                'type' => 'string',
            ),
        );
    }
}
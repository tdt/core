<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\SparqlDefinitionRepositoryInterface;

class SparqlDefinitionRepository extends BaseDefinitionRepository implements SparqlDefinitionRepositoryInterface
{

    protected $rules = array(
        'endpoint' => 'required',
        'query' => 'required|sparqlquery',
        'description' => 'required',
    );

    public function __construct(\SparqlDefinition $model)
    {
        $this->model = $model;
    }

    public function store(array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        $query = $input['query'];

        if (stripos($query, "select") !== false) { // SELECT query
            $input['query_type'] = "select";
        } elseif (stripos($query, "construct") !== false) { // CONSTRUCT query
            $input['query_type'] = "construct";
        }

        return $this->model->create($input);
    }

    public function update($model_id, array $input)
    {
        // Process input (e.g. set default values to empty properties)
        $input = $this->processInput($input);

        $model_object = $this->model->find($model_id);

        $query = $input['query'];

        if (stripos($query, "select") !== false) { // SELECT query
            $input['query_type'] = "select";
        } elseif (stripos($query, "construct") !== false) { // CONSTRUCT query
            $input['query_type'] = "construct";
        }

        $model_object->update($input);

        return $model_object->toArray();
    }

    /**
     * Retrieve the set of create parameters that make up a SPARQL definition.
     */
    public function getCreateParameters()
    {
        return array(
            'endpoint' => array(
                'required' => true,
                'name' => 'SPARQL endpoint',
                'description' => 'The uri of the SPARQL end-point (e.g. http://foobar:8890/sparql-auth).',
                'type' => 'string',
            ),
            'title' => array(
                'required' => true,
                'name' => 'Title',
                'description' => 'A name given to the resource.',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'text',
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

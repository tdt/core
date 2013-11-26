<?php

/**
 * SPARQL definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class SparqlDefinition extends SourceType{

    protected $table = 'sparqldefinitions';

    protected $fillable = array('endpoint', 'query', 'endpoint_user', 'endpoint_password', 'description');

    /**
     * Validate the input for this model.
     * TODO validate the endpoint with user and password if provided.
     */
    public static function validate($params){
        return parent::validate($params);
    }

    /**
     * Relationship with the Definition model.
     */
    public function definition(){
        return $this->morphOne('Definition', 'source');
    }

    /**
     * Retrieve the set of create parameters that make up a SPARQL definition.
     */
    public static function getCreateParameters(){
        return array(
            'endpoint' => array(
                'required' => true,
                'description' => 'The uri of the SPARQL end-point (e.g. http://foobar:8890/sparql-auth).',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'string',
            ),
            'query' => array(
                'required' => true,
                'description' =>  'The query to be executed.',
                'type' => 'string',
            ),
            'endpoint_user' => array(
                'required' => false,
                'description' => 'Username of the user that has sufficient rights to query the sparql endpoint.',
                'type' => 'string',
            ),
            'endpoint_password' => array(
                'required' => false,
                'description' => 'Password of the provided user to query a sparql endpoint.',
                'type' => 'string',
            ),
        );
    }

    /**
     * Retrieve the set of create parameters that make up a SPARQL definition.
     * Include the parameters that make up relationships with this model.
     */
    public static function getAllParameters(){
        return self::getCreateParameters();
    }

   /**
     * Retrieve the set of validation rules for every create parameter.
     * If the parameters doesn't have any rules, it's not mentioned in the array.
     */
    public static function getCreateValidators(){
        return array(
            'endpoint' => 'required',
            'query' => 'required|sparqlquery',
            'description' => 'required',
        );
    }
}

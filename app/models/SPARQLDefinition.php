<?php

/**
 * SPARQL definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class SparqlDefinition extends SourceType{

    protected $table = 'sparqldefinitions';

    protected $guarded = array('id');

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
    public static function getCreateProperties(){
        return array(
            'endpoint' => array(
                'required' => true,
                'description' => 'The uri of the SPARQL end-point (e.g. http://thisisanendpoint:8890/sparql-auth',
            ),
            'description' => array(
                'required' => true,
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
            ),
            'query' => array(
                'required' => true,
                'description' =>  'The query to be executed.' // TODO check on how this query should be encoded!
            ),
            'endpoint_user' => array(
                'required' => false,
                'description' => 'Username of the user that has sufficient rights to query the sparql endpoint.',
            ),
            'endpoint_password' => array(
                'required' => false,
                'description' => 'Password of the provided user to query a sparql endpoint.',
            ),
        );
    }

    /**
     * Retrieve the set of create parameters that make up a SPARQL definition.
     * Include the parameters that make up relationships with this model.
     */
    public static function getAllProperties(){
        return self::getCreateProperties();
    }

   /**
     * Retrieve the set of validation rules for every create parameter.
     * If the parameters doesn't have any rules, it's not mentioned in the array.
     */
    public static function getCreateValidators(){
        return array(
            'endpoint' => 'required',
            'query' => 'required',
        );
    }
}
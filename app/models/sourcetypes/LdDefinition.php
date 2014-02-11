<?php

/**
 * Linked Data definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class LdDefinition extends SourceType{

    protected $table = 'lddefinitions';

    protected $fillable = array('endpoint', 'endpoint_user', 'endpoint_password', 'description');

    /**
     * Validate the input for this model.
     * TODO validate the endpoint with user and password if provided.
     */
    public static function validate($params){
        return parent::validate($params);
    }

    /**
     * Retrieve the set of create parameters that make up a Linked Data definition.
     */
    public static function getCreateParameters(){
        return array(
            'endpoint' => array(
                'required' => true,
                'name' => 'SPARQL endpoint',
                'description' => 'The uri of the Linked Data end-point (e.g. http://foobar:8890/sparql-auth)',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'string',
            ),
            'endpoint_user' => array(
                'required' => false,
                'name' => 'SPARQL endpoint user',
                'description' => 'Username of the user that has sufficient rights to query the Linked Data endpoint.',
                'type' => 'string',
            ),
            'endpoint_password' => array(
                'required' => false,
                'name' => "SPARQL endpoint user's password",
                'description' => 'Password of the provided user to query a Linked Data endpoint.',
                'type' => 'string',
            ),
        );
    }

    /**
     * Retrieve the set of create parameters that make up a Linked Data definition.
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
            'description' => 'required',
        );
    }
}

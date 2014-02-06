<?php

/**
 * Turtle definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class RdfDefinition extends SourceType{

    protected $table = 'rdfdefinitions';

    protected $fillable = array('uri', 'description');

    /**
     * Validate the input for this model
     */
    public static function validate($params){
        return parent::validate($params);
    }

    /**
     * Relationship with the Definition model
     */
    public function definition(){
        return $this->morphOne('Definition', 'source');
    }

    /**
     * Retrieve the set of create parameters that make up a Linked Data definition
     */
    public static function getCreateParameters(){
        return array(
            'uri' => array(
                'required' => true,
                'name' => 'URI',
                'description' => 'The URI of the turtle file.',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'string',
            ),
            'format' => array(
                'required' => true,
                'name' => 'Format',
                'description' => 'The format of your RDF content, Turtle, XML, ...',
                'type' => 'string',
            ),
        );
    }

    /**
     * Retrieve the set of create parameters that make up a Linked Data definition
     * Include the parameters that make up relationships with this model
     */
    public static function getAllParameters(){
        return self::getCreateParameters();
    }

   /**
     * Retrieve the set of validation rules for every create parameter
     * If the parameters doesn't have any rules, it's not mentioned in the array
     */
    public static function getCreateValidators(){
        return array(
            'uri' => 'required|uri',
            'description' => 'required',
        );
    }
}

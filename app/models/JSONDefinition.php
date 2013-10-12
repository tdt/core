<?php

/**
 * JSON definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class JsonDefinition extends Eloquent{

    protected $table = 'jsondefinitions';

    protected $guarded = array('id');

    /**
     * Retrieve the set of create parameters that make up a JSON definition.
     */
    public static function getCreateParameters(){
        return array(
            'uri' => array(                
                'required' => true,
                'description' => 'The location of the JSON file, this should either be a URL or a local file location.',
            ),
        );
    }

   /**
     * Retrieve the set of validation rules for every create parameter.
     * If the parameters doesn't have any rules, it's not mentioned in the array.
     */ 
    public static function getCreateValidators(){
        return array();
    }
}
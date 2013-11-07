<?php

/**
 * Installed definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class InstalledDefinition extends SourceType{

    protected $table = 'installeddefinitions';

    protected $fillable = array('path', 'description');

    /**
     * Relationship with the Definition model.
     */
    public function definition(){
        return $this->morphOne('Definition', 'source');
    }

    /**
     * Validate the input for this model.
     */
    public static function validate($params){
        return parent::validate($params);
    }

    /**
     * Retrieve the set of create parameters that make up a JSON definition.
     */
    public static function getCreateParameters(){
        return array(
            'class' => array(
                'required' => true,
                'description' => 'The name of the class',
            ),
            'path' => array(
                'required' => true,
                'description' => 'The location of the class file, relative from the "/installed" folder.',
            ),
            'description' => array(
                'required' => true,
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
            )
        );
    }

    /**
     * Retrieve the set of create parameters that make up an installed definition.
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
            'class' => 'required',
            'path' => 'required',
            'description' => 'required',
        );
    }
}
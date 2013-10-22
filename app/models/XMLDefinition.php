<?php

/**
 * XMl definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class XmlDefinition extends SourceType{

    protected $table = 'xmldefinitions';

    protected $guarded = array('id');

    /**
     * Relationship with the Definition model.
     */
    public function definition(){
        return $this->morphOne('Definition');
    }

    /**
     * Validate the input for this model.
     */
    public static function validate($params){
        return parent::validate($params);
    }

    /**
     * Retrieve the set of create parameters that make up a XML definition.
     */
    public static function getCreateProperties(){
        return array(
            'uri' => array(
                'required' => true,
                'description' => 'The location of the XML file, this should either be a URL or a local file location.',
            ),
            'documentation' => array(
                'required' => true,
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
            )
        );
    }

     /**
     * Retrieve the set of create parameters that make up a XML definition.
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
            'uri' => 'uri',
        );
    }
}
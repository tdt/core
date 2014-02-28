<?php

/**
 * XMl definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class XmlDefinition extends SourceType{

    protected $table = 'xmldefinitions';

    protected $fillable = array('uri', 'description');

    /**
     * Validate the input for this model.
     */
    public static function validate($params){
        return parent::validate($params);
    }

    /**
     * Retrieve the set of create parameters that make up a XML definition.
     */
    public static function getCreateParameters(){
        return array(
            'uri' => array(
                'required' => true,
                'name' => 'URI',
                'description' => 'The location of the XML file, this should either be a URL or a local file location.',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'string',
            )
        );
    }

     /**
     * Retrieve the set of create parameters that make up a XML definition.
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
            'uri' => 'uri|required',
            'description' => 'required',
        );
    }
}

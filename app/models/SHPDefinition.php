<?php

/**
 * Shape definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class ShpDefinition extends SourceType{

    protected $table = 'shpdefinitions';

    protected $guarded = array('id');

    public function tabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }

    /**
     * Validate the input for this model.
     */
    public static function validate($params){
        return parent::validate($params);
    }

    /**
     * Retrieve the set of create parameters that make up a SHP definition.
     */
    public static function getCreateParameters(){
        return array(
            'uri' => array(
                'required' => true,
                'description' => 'The location of the SHP file, either a URL or a local file location.',
                ),
            'epsg' => array(
                'required' => false,
                'description' => 'This parameter holds the EPSG code in which the geometric properties in the shape file are encoded.',
                'default_value' => 4326
                ),
            'documentation' => array(
                'required' => true,
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
            )
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
<?php

/**
 * Shape definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class SHPDefinition extends Eloquent{

    protected $table = 'shpdefinitions';

    protected $guarded = array('id');

    public function tabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }

    /**
     * Retrieve the set of create parameters that make up a SHP definition.
     */
    public static function getCreateParameters(){
        return array(
            array(
                'name' => 'uri',
                'required' => true,
                'description' => 'The location of the SHP file, either a URL or a local file location.',
                ),
            array(
                'name' => 'epsg',
                'required' => false,
                'description' => 'This parameter holds the EPSG code in which the geometric properties in the shape file are encoded.',
                'default_value' => 4326
                ),
            );
    }
}
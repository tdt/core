<?php

/**
 * Tabular columns model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class GeoProperty extends Eloquent{

    protected $table = 'geoproperties';

    protected $fillable = array('path', 'geo_property');

    /**
     * Return the polymorphic relation with a source type.
     */
    public function source(){
        return $this->morphTo();
    }

    /**
     * Retrieve the set of create parameters that make up a TabularColumn model.
     */
    public static function getCreateProperties(){

        return array(
            'geo_property' => array(
                'required' => false,
                'description' => "geo_property should be an array holding one of the following values ['latitude'|'longitude'|'point'|'polygon'|'linestring'],
                the value should then be the name of the key holding that geo property.",
            ),
        );
    }

    /**
     * Validate the parameters.
     */
    public static function validate($params){


        return true;
    }
}

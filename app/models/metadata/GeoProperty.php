<?php

/**
 * Geo properties model.
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class GeoProperty extends Eloquent{

    protected $table = 'geoproperties';

    protected $fillable = array('path', 'geo_property');

    public static $GEOTYPES = array('polygon', 'latitude', 'longitude', 'polyline', 'multiline', 'point');

    /**
     * Return the polymorphic relation with a source type.
     */
    public function source(){
        return $this->morphTo();
    }

    /**
     * Retrieve the set of create parameters that make up a TabularColumn model.
     */
    public static function getCreateParameters(){

        return array(
            'geo_property' => array(
                'required' => false,
                'description' => "geo_property should be an array holding one of the following values ['latitude'|'longitude'|'point'|'polygon'|'polyline'],
                the value should then be the name of the key holding that geo property.",
            ),
        );
    }

    /**
     * Validate the parameters.
     */
    public static function validate($params){

        if(!empty($params['geo_property'])){
            $params = $params['geo_property'];
            foreach($params as $geo_type => $column_name){

                $type = mb_strtolower($geo_type);
                if(!in_array($type, self::$GEOTYPES)){

                    $types = implode(', ', self::$GEOTYPES);
                    \App::abort(452, "The given geo type ($geo_type) is not supported, the supported list is: $types.");
                }
            }
        }
    }
}

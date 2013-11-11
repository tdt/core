<?php

/**
 * Geo properties model.
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class GeoProperty extends Eloquent{

    protected $table = 'geoproperties';

    protected $fillable = array('path', 'property');

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

        $geo_type_string = implode(',', self::$GEOTYPES);

        return array(
            'property' => array(
                'required' => false,
                'description' => "This must be a string holding one of the following values $geo_type_string.",
            ),
            'path' => array(
                'required' => false,
                'description' => 'This takes on the path to the value of the property, for tabular data for example this will be the name of the column that holds the property value.',
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

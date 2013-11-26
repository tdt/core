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
                'type' => 'string',
            ),
            'path' => array(
                'required' => false,
                'description' => 'This takes on the path to the value of the property, for tabular data for example this will be the name of the column that holds the property value.',
                'type' => 'string',
            ),
        );
    }

    /**
     * Return the set of rules for the parameters for validation purposes.
     */
    public static function getCreateValidators(){
        return array(
            'property' => 'required',
            'path' => 'required',
        );
    }

    /**
     * Validate the parameters.
     */
    public static function validate($params){

        // If no geo parameters are passed, return
        if(empty($params)){
            return;
        }

        foreach($params as $geo){

            // Validate the parameters to their rules
            $validator = Validator::make(
                            $geo,
                            self::getCreateValidators()
                        );

            // If any validation fails, return a message and abort the workflow
            if($validator->fails()){

                $messages = $validator->messages();
                \App::abort(400, $messages->first());
            }

            // Checkc if the given type is valid

            $type = mb_strtolower($geo['property']);
            if(!in_array($type, self::$GEOTYPES)){

                $types = implode(', ', self::$GEOTYPES);
                \App::abort(400, "The given geo type ($type) is not supported, the supported list is: $types.");
            }
        }
    }
}

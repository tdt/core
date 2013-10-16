<?php

/**
 * Base
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class SourceType extends Eloquent{

    public static function validate($params){

        $validated_params = array();

        $create_params = self::getCreateParameters();
        $rules = self::getCreateValidators();

        foreach($create_params as $key => $info){

            if(!array_key_exists($key, $params)){

                if(!empty($info['required']) && $info['required']){
                    \App::abort(452, "The parameter $key is required in order to create a defintion but was not provided.");
                }

                if(!empty($info['default_value'])){
                    $validated_params[$key] = $info['default_value'];
                }else{
                    $validated_params[$key] = null;
                }
            }else{

                if(!empty($rules[$key])){

                    $validator = \Validator::make(
                        array($key => $params[$key]),
                        array($key => $rules[$key])
                        );

                    if($validator->fails()){
                        \App::abort(452, "The validation failed for parameter $key, make sure the value is valid.");
                    }
                }

                $validated_params[$key] = $params[$key];
            }
        }

        return $validated_params;
    }
}
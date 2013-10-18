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

        array_keys($create_params);

        // Validate the parameters to their rules.
        $validator = Validator::make(
                        $params,
                        $rules,
                        self::getErrorMessages()
        );

        // If any validation fails, return a message and abort the workflow.
        if($validator->fails()){

            $messages = $validator->messages();
            \App::abort(452, $messages->first());
        }

        // Return the parameters with their validated/default values.
        foreach($create_params as $key => $info){

            if(!empty($params[$key])){
                $validated_params[$key] = $params[$key];
            }else if(!empty($info['default_value'])){
                $validated_params[$key] = $info['default_value'];
            }else{
                $validated_params[$key] = null;
            }
        }

        return $validated_params;
    }

    /**
     * Retrieve the collection of custom error messages for validation.
     */
    public static function getErrorMessages(){
        return array(
            'uri' => "The uri provided could not be retrieved, if it is a file location, try putting file:// in front of the path.",
        );
    }
}
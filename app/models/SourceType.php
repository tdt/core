<?php

/**
 * Base model for every publishable source (CSV, SHP, ...).
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class SourceType extends Eloquent{

    public function getType(){
        // Return bare type (CSV, JSON)
        return str_replace('DEFINITION', '', strtoupper(get_called_class()));
    }

    public static function validate($params){

        $validated_params = array();

        $create_params = self::getCreateParameters();

        $rules = self::getCreateValidators();

        // Validate the parameters to their rules
        $validator = Validator::make(
                        $params,
                        $rules,
                        self::getErrorMessages()
        );

        // If any validation fails, return a message and abort the workflow
        if($validator->fails()){

            $messages = $validator->messages();
            \App::abort(400, $messages->first());
        }

        // Return the parameters with their validated/default values
        foreach($create_params as $key => $info){

            if(!empty($params[$key])){
                $validated_params[$key] = $params[$key];
            }else if(!empty($info['default_value']) || is_numeric(@$info['default_value'])){
                $validated_params[$key] = @$info['default_value'];
            }else{
                $validated_params[$key] = null;
            }
        }

        return $validated_params;
    }

    /**
     * Update a SourceType
     * Overwrite the update of the Eloquent Model
     * defaults to the save() method.
     */
    public function update( array $attr = array()){
        $this->save();
    }

    /**
     * Retrieve the collection of custom error messages for validation.
     */
    public static function getErrorMessages(){
        return array(
            'uri' => "The uri provided could not be resolved.",
            'file' => 'The uri provided could not be resolved, if the uri is a system path try putting file:// in front of it.',
            'json' => 'The contents of the uri could not be parsed as JSON, make sure the JSON is valid.',
            'sparqlquery' => "The query could not be validated, make sure you don't use a limit and/or offset statement and that a select or construct statement is present.",
        );
    }
}

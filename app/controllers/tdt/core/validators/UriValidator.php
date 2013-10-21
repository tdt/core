<?php

namespace tdt\core\validators;

class CustomValidator extends \Illuminate\Validation\Validator {

    public function validateUri($attribute, $value, $parameters){

        try{

            file_get_contents($value);
            return true;
        }catch(\Exception $ex){
            return false;
        }
    }

    public function validateFile($attribute, $value, $parameters){

        try{

            $handle = fopen($value, 'r');
            return $handle;
        }catch(\Exception $ex){
            return false;
        }
    }
}
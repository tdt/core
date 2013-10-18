<?php

namespace tdt\core\validators;

class UriValidator extends \Illuminate\Validation\Validator {

    public function validateUri($attribute, $value, $parameters){

        try{

            file_get_contents($value);
            return true;
        }catch(\Exception $ex){
            return false;
        }
    }
}
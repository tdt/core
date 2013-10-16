<?php

namespace tdt\core\validators;

class UriValidator extends \Illuminate\Validation\Validator {

    public function validateUri($attribute, $value, $parameters){

        return file_get_contents($value);
    }
}
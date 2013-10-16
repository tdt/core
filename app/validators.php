<?php

use tdt\core\validators\UriValidator;

Validator::resolver(function($translator, $data, $rules, $messages){
    return new UriValidator($translator, $data, $rules, $messages);
});

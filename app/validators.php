<?php

use Tdt\Core\Validators\CustomValidator;

Validator::resolver(function ($translator, $data, $rules, $messages) {
    return new CustomValidator($translator, $data, $rules, $messages);
});

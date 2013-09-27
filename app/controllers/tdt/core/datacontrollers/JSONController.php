<?php

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;

class JSONController implements IDataController {

    public function readData($source_definition, $parameters = null){
        var_dump($source_definition);
        exit();
    }

}
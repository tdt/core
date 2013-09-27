<?php

namespace tdt\core\datacontrollers;

interface IDataController {

    public function readData($source_definition, $parameters = null);

}
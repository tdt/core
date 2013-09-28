<?php

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;

/**
 * CSV Controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class JSONController implements IDataController {

    public function readData($source_definition, $parameters = null){
        var_dump($source_definition);
        exit();
    }

}
<?php

namespace tdt\commands\ie;

use tdt\core\definitions\DefinitionController;

/**
 * Import/export definitions
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class Definitions implements IImportExport {

    public static function import($data){

    }

    public static function export($identifier = null){
        if(empty($identifier)){
            // Request all of the definitions
            return DefinitionController::getAllDefinitions();
        }else{
            // Request a single definition
            $definition =  DefinitionController::get($identifier);
            return array($identifier => $definition->getAllParameters());
        }
    }

}


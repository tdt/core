<?php

namespace tdt\core;

use tdt\core\datasets\DatasetController;
use tdt\core\definitions\DiscoveryController;
use tdt\core\definitions\DefinitionController;

/**
 * The base controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class BaseController extends \Controller {

    /*
     * Handles all core requests
     */
    public function handleRequest($uri){

        // None of the above -> must be a dataset request        
        return DefinitionController::handle($uri);
        //return DiscoveryController::handle($uri);
        //return DatasetController::handle($uri);
    }

}
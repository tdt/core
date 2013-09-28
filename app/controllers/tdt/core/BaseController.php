<?php

namespace tdt\core;

use tdt\core\datasets\DatasetController;

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
        return DatasetController::handle($uri);

    }

}
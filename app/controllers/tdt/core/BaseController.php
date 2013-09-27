<?php

namespace tdt\core;

use tdt\core\datasets\DatasetController;

class BaseController extends \Controller {

    /*
     * Handles all core requests
     */
    public function handleRequest($uri){

        // None of the above -> must be a dataset request
        DatasetController::handle($uri);

    }

}
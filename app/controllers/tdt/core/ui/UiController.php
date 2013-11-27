<?php

/**
 * The
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace tdt\core\ui;

use tdt\core\auth\Auth;

class UiController extends \Controller {

    public static function handle($uri){

        // Switch to correct controller
        switch (\Request::segment(2)) {
            case 'users':
                $response = UserController::handle($uri);
                break;

            case 'datasets':
                $response = DatasetController::handle($uri);
                break;

            default:
                // Redirect to default admin page
                return \Redirect::to('admin/datasets');
                break;
        }

        return $response;
    }

}

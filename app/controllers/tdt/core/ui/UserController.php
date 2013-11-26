<?php

/**
 * The usercontroller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace tdt\core\ui;

use tdt\core\auth\Auth;

class UserController extends \Controller {

    /**
     * Admin.user.view
     */
    public function getIndex(){
        // Set permission
        Auth::requirePermissions('admin.user.view');

        // Get all definitions
        $definitions = \Definition::all();

        return \View::make('ui.users.list')
                    ->with('title', 'The Datatank')
                    ->with('definitions', $definitions);

        return \Response::make($view);
    }

    /**
     * Admin.user.delete
     */
    public function getDelete($id){

        // Set permission
        Auth::requirePermissions('admin.user.delete');

        try{
            // Find the user using the user id
            $user = Sentry::findUserById($id);

            // Delete the user
            $user->delete();

        }catch (Cartalyst\Sentry\Users\UserNotFoundException $e){
            // Ignore and redirect back
        }

        return \Redirect::to('api/admin/users');
    }

}
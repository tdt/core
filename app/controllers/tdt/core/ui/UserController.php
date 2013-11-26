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

        // Get all users
        $users = \Sentry::findAllUsers();

        // Get all groups
        $groups = \Sentry::findAllGroups();


        return \View::make('ui.users.list')
                    ->with('title', 'The Datatank')
                    ->with('users', $users)
                    ->with('groups', $groups);

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
            $user = \Sentry::findUserById($id);

            // Delete the user
            $user->delete();

        }catch (Cartalyst\Sentry\Users\UserNotFoundException $e){
            // Ignore and redirect back
        }

        return \Redirect::to('api/admin/users');
    }

    /**
     * Admin.user.create
     */
    public function postCreate(){

        // Set permission
        Auth::requirePermissions('admin.user.create');


        try{

            // Find the group using the group id
            $group = \Sentry::findGroupById(\Input::get('group'));

            // Create the user
            $user = \Sentry::createUser(array(
                'email'    => \Input::get('name'),
                'password' => \Input::get('password'),
            ));

            // Activate the user
            $user->activated = 1;
            $user->save();

            // Assign the group to the user
            $user->addGroup($group);

        }catch (\Cartalyst\Sentry\Users\LoginRequiredException $e){

        }catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e){

        }catch (\Cartalyst\Sentry\Users\UserExistsException $e){

        }catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e){

        }


        return \Redirect::to('api/admin/users');
    }

}
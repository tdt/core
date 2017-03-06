<?php

/**
 * The usercontroller
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace Tdt\Core\Ui;

use Tdt\Core\Auth\Auth;
use Tdt\Core\Ui\Helpers\Flash;

class UserController extends UiController
{

    /**
     * Admin.user.view
     */
    public function getIndex()
    {
        // Set permission
        Auth::requirePermissions('admin.user.view');

        // Get all users
        $users = \Sentry::findAllUsers();

        // Get all groups
        $groups = \Sentry::findAllGroups();

        // Get error
        $error = Flash::get();

        $view =  \View::make('ui.users.list')
                    ->with('title', 'User management | The Datatank')
                    ->with('users', $users)
                    ->with('groups', $groups)
                    ->with('error', $error);

        return \Response::make($view);
    }

    /**
     * Admin.user.delete
     */
    public function getDelete($id)
    {

        // Set permission
        Auth::requirePermissions('admin.user.delete');

        try {
            // Find the user using the user id
            $user = \Sentry::findUserById($id);

            if ($user->id > 2) {
                // Delete the user
                $user->delete();
            }

        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            // Ignore and redirect back
        }

        return \Redirect::to('api/admin/users');
    }

    /**
     * Admin.user.create
     */
    public function postCreate()
    {

        // Set permission
        Auth::requirePermissions('admin.user.create');


        try {

            // Find the group using the group id
            $group = \Sentry::findGroupById(\Input::get('group'));

            // Create the user
            $user = \Sentry::createUser(array(
                'email'    => strtolower(\Input::get('name')),
                'password' => \Input::get('password'),
            ));

            // Activate the user
            $user->activated = 1;
            $user->save();

            // Assign the group to the user
            $user->addGroup($group);

        } catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
            Flash::set('Username is required');
        } catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e) {
            Flash::set('A password is required');
        } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
            Flash::set('A user with that username already exists');
        } catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            // Illegal group -> ignore
        }


        return \Redirect::to('api/admin/users');
    }

    /**
     * Admin.user.update
     */
    public function postUpdate($id = null)
    {

        // Set permission
        Auth::requirePermissions('admin.user.update');

        try {
            if (empty($id)) {
                $id = \Input::get('id');
            }
            // Find the user using the user id
            $user = \Sentry::findUserById($id);

            // Update account
            if ($id > 2 && \Input::get('name')) {
                $user->email = strtolower(\Input::get('name'));
            }

            // Update password (not for the everyone account)
            if ($id > 1 && \Input::get('password')) {
                $resetCode = $user->getResetPasswordCode();
                $user->attemptResetPassword($resetCode, \Input::get('password'));
            }

            $user->save();

            // Find the group using the group id
            $group = \Sentry::findGroupById(\Input::get('group'));

            if ($id > 2) {
                // Remove user from previous groups
                foreach ($user->getGroups() as $g) {
                    $user->removeGroup($g);
                }

                // Assign the group to the user
                $user->addGroup($group);
            }

        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            // Ignore and redirect back
        } catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            // Ignore and redirect back
        }

        return \Redirect::to('api/admin/users');
    }
}

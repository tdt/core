<?php

/**
 * The group controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace tdt\core\ui;

use tdt\core\auth\Auth;
use tdt\core\ui\helpers\Flash;

class GroupController extends \Controller {

    /**
     * Admin.group.view
     */
    public function getIndex(){
        // Set permission
        Auth::requirePermissions('admin.group.view');

        // Get all users
        $users = \Sentry::findAllUsers();

        // Get all groups
        $groups = \Sentry::findAllGroups();

        // Get error
        $error = Flash::get();

        return \View::make('ui.groups.list')
                    ->with('title', 'The Datatank')
                    ->with('users', $users)
                    ->with('groups', $groups)
                    ->with('error', $error);

        return \Response::make($view);
    }

    /**
     * Admin.group.delete
     */
    public function getDelete($id){

        // Set permission
        Auth::requirePermissions('admin.group.delete');

        try{
            // Find the group using the group id
            $group = \Sentry::findGroupById($id);

            if($group->id > 2){
                // Delete the group
                $group->delete();
            }

        }catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e){
            // Ignore and redirect back
        }

        return \Redirect::to('api/admin/groups');
    }

    /**
     * Admin.group.create
     */
    public function postCreate(){

        // Set permission
        Auth::requirePermissions('admin.group.create');

        try{

            // Create the group
            $group = \Sentry::createGroup(array(
                'name'        => \Input::get('name'),
            ));

        }catch (\Cartalyst\Sentry\Groups\NameRequiredException $e){
            Flash::set('Name is required');
        }catch (\Cartalyst\Sentry\Groups\GroupExistsException $e){
            Flash::set('A group with that name already exists');
        }

        return \Redirect::to('api/admin/groups');
    }

    /**
     * Admin.group.update
     */
    public function postUpdate($id = null){

        // Set permission
        Auth::requirePermissions('admin.group.update');

        try{
            if(empty($id)){
                $id = \Input::get('id');
            }
            // Find the user using the user id
            $user = \Sentry::findUserById($id);

            // Update account
            if($id > 2 && \Input::get('name')){
                $user->email = \Input::get('name');
            }

            // Update password (not for the everyone account)
            if($id > 1 && \Input::get('password')){
                $resetCode = $user->getResetPasswordCode();
                $user->attemptResetPassword($resetCode, \Input::get('password'));
            }

            $user->save();

            // Find the group using the group id
            $group = \Sentry::findGroupById(\Input::get('group'));

            if($id > 2){
                // Remove user from previous groups
                foreach($user->getGroups() as $g){
                    $user->removeGroup($g);
                }

                // Assign the group to the user
                $user->addGroup($group);
            }

        }catch (\Cartalyst\Sentry\Users\UserNotFoundException $e){
            // Ignore and redirect back
        }catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e){
            // Ignore and redirect back
        }

        return \Redirect::to('api/admin/groups');
    }
}
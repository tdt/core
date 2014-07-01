<?php

/**
 * The group controller
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace Tdt\Core\Ui;

use Tdt\Core\Auth\Auth;
use Tdt\Core\Ui\Helpers\Flash;

class GroupController extends UiController
{

    /**
     * Admin.group.view
     */
    public function getIndex()
    {
        // Set permission
        Auth::requirePermissions('admin.group.view');

        // Get all users
        $users = \Sentry::findAllUsers();

        // Get all groups
        $groups = \Sentry::findAllGroups();

        // Get all permissions
        $permission_groups = \Config::get('permissions');
        $input_permission_groups = \Config::get('tdt/input::permissions');
        if (!empty($input_permission_groups)) {
            $permission_groups = array_merge($permission_groups, $input_permission_groups);
        }

        // Get error
        $error = Flash::get();

        return \View::make('ui.groups.list')
                    ->with('title', 'Group management | The Datatank')
                    ->with('users', $users)
                    ->with('groups', $groups)
                    ->with('permission_groups', $permission_groups)
                    ->with('error', $error);

        return \Response::make($view);
    }

    /**
     * Admin.group.delete
     */
    public function getDelete($id)
    {

        // Set permission
        Auth::requirePermissions('admin.group.delete');

        try {
            // Find the group using the group id
            $group = \Sentry::findGroupById($id);

            if ($group->id > 2) {
                // Delete the group
                $group->delete();
            }

        } catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            // Ignore and redirect back
        }

        return \Redirect::to('api/admin/groups');
    }

    /**
     * Admin.group.create
     */
    public function postCreate()
    {

        // Set permission
        Auth::requirePermissions('admin.group.create');

        try {

            // Create the group
            $group = \Sentry::createGroup(array(
                'name'        => \Input::get('name'),
            ));

        } catch (\Cartalyst\Sentry\Groups\NameRequiredException $e) {
            Flash::set('Name is required');
        } catch (\Cartalyst\Sentry\Groups\GroupExistsException $e) {
            Flash::set('A group with that name already exists');
        }

        return \Redirect::to('api/admin/groups');
    }

    /**
     * Admin.group.update
     */
    public function postUpdate($id = null)
    {

        // Set permission
        Auth::requirePermissions('admin.group.update');

        try {
            if (empty($id)) {
                $id = \Input::get('id');
            }
            // Find the user using the group id
            $group = \Sentry::findGroupById($id);

            $permissions_save = \Input::get('btn_save_permissions');

            if (empty($permissions_save)) {

                // Update group
                if ($id > 2) {
                    $group->name = \Input::get('name');
                }
                $group->save();
            } else {

                if ($group->id > 2) {
                    // Update permissions
                    $permission_data = \Input::get();
                    $permissions = array();

                    // Unset previous permissions
                    $group_permissions = $group->getPermissions();
                    foreach ($group_permissions as $p => $value) {
                        $permissions[$p] = 0;
                    }

                    // Add new ones
                    foreach ($permission_data as $p => $value) {

                        // Skip extra information
                        if ($p == 'id' || $p == 'btn_save_permissions') {
                            continue;
                        }

                        // Form undo transform
                        $p = str_replace('_', '.', $p);

                        // Permission set
                        $permissions[$p] = 1;

                    }

                    // Save permissions
                    $group->permissions = $permissions;
                    $group->save();
                }

            }

        } catch (\Cartalyst\Sentry\Groups\NameRequiredException $e) {
            Flash::set('Name is required');
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            // Ignore and redirect back
        } catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            // Ignore and redirect back
        }

        return \Redirect::to('api/admin/groups');
    }
}

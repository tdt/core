<?php

namespace tdt\commands\ie;

/**
 * Import/export users
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class Users implements IImportExport
{

    public static function import($users){

        $messages = array();

        foreach ($users as $user) {
            $groups = $user['groups'];
            $primary_group = array_shift($groups);

            // Unset the unnecessary fields
            unset($user['id']);
            unset($user['groups']);

            try {
                // Create the user
                $created_user = \Sentry::createUser($user);

                // Manually update password
                \DB::table('users')
                    ->where('id', $created_user->id)
                    ->update(array('password' => $user['password']));

                // Try adding user to groups
                try {
                    // Find the group using the group name
                    $group = \Sentry::findGroupByName($primary_group);

                    // Assign the group to the user
                    $created_user->addGroup($group);
                } catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
                    echo "Group '$primary_group' was not found.";
                }

                $messages[$user['email']] = true;
            } catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
                $messages[$user['email']] = false;
            } catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e) {
                $messages[$user['email']] = false;
            } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
                $messages[$user['email']] = false;
            }
        }

        return $messages;
    }

    public static function export($identifier = null){
        // Request all the users
        $sentry_data = \Sentry::findAllUsers();

        // Push them in an array
        $users = array();
        foreach ($sentry_data as $u) {

            // Get user's groups
            $sentry_groups = $u->getGroups();

            // Include password hash
            $password = $u->password;

            // Transform to array
            $u = $u->toArray();

            // Add password hash
            $u['password'] = $password;

            // Add group names
            $u['groups'] = array();

            foreach ($sentry_groups as $g) {
                array_push($u['groups'], $g->name);
            }

            array_push($users, $u);
        }

        return $users;
    }
}

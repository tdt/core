<?php

namespace tdt\commands\ie;

/**
 * Import/export users
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class Users implements IImportExport {

    public static function import($data){

    }

    public static function export($identifier = null){
        // Request all the users
        $sentry_data = \Sentry::findAllUsers();

        // Push them in an array
        $users = array();
        foreach($sentry_data as $u){

            // Get user's groups
            $sentry_groups = $u->getGroups();

            // Include password hash
            $password = $u->password;

            // Transform to array
            $u = $u->toArray();

            // Add password hash
            $u['password'] = $password;

            // Add group IDs
            $u['groups'] = array();

            foreach($sentry_groups as $g){
                array_push($u['groups'], $g->id);
            }

            array_push($users, $u);
        }

        return $users;
    }

}


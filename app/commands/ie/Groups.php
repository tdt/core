<?php

namespace tdt\commands\ie;

/**
 * Import/export groups
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class Groups implements IImportExport
{

    public static function import($groups){

        $messages = array();

        foreach ($groups as $group) {

            // Unset the ID
            unset($group['id']);

            try {
                // Create the group
                $group = \Sentry::createGroup($group);
                $messages[$group['name']] = true;
            } catch (\Cartalyst\Sentry\Groups\NameRequiredException $e) {
                $messages[$group['name']] = false;
            } catch (\Cartalyst\Sentry\Groups\GroupExistsException $e) {
                $messages[$group['name']] = false;
            }
        }

        return $messages;
    }

    public static function export($identifier = null){

        // Request all the group
        $sentry_data = \Sentry::findAllGroups();

        // Push them in an array
        $groups = array();
        foreach ($sentry_data as $g) {
            array_push($groups, $g->toArray());
        }

        return $groups;
    }
}

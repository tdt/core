<?php

namespace tdt\commands\ie;

/**
 * Import/export groups
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class Groups implements IImportExport {

    public static function import($data){

    }

    public static function export($identifier = null){
        // Request all the group
        $sentry_data = \Sentry::findAllGroups();

        // Push them in an array
        $groups = array();
        foreach($sentry_data as $g){
            array_push($groups, $g->toArray());
        }

        return $groups;
    }

}


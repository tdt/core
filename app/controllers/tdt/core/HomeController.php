<?php

namespace tdt\core;

/**
 * HomeController
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class HomeController extends \Controller {

    public static function handle($uri){
        $definitions = \Definition::all();

        return \View::make('home')->with('title', 'The Datatank')
                                  ->with('definitions', $definitions);
    }
}

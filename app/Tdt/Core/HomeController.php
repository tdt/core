<?php

namespace Tdt\Core;

/**
 * HomeController
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class HomeController extends \Controller
{

    public static function handle($uri)
    {
        $definitions = \Definition::all();

        $view = \View::make('home')->with('title', 'Datasets | The Datatank')
                                  ->with('page_title', 'Datasets')
                                  ->with('definitions', $definitions);

        return \Response::make($view);
    }
}

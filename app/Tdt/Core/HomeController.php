<?php

namespace Tdt\Core;

/**
 * HomeController
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class HomeController extends \Controller
{

    public static function handle($uri)
    {
        $definitions = \Definition::all();

        // Get unique properties
        $languages = array_filter(array_unique(array_column($definitions->toArray(), 'language')));
        $licenses = array_filter(array_unique(array_column($definitions->toArray(), 'rights')));
        $themes = array_filter(array_unique(array_column($definitions->toArray(), 'theme')));

        // Get unique publishers
				$publishers = [];
				foreach ($definitions as $def) {
					if (!empty($def['publisher_uri']) && !empty($def['publisher_name'])) {
						$publishers[$def['publisher_uri']] = $def['publisher_name'];
					}
				}

        $view = \View::make('home')->with('title', 'Datasets | The Datatank')
                                  ->with('page_title', 'Datasets')
                                  ->with('languages', $languages)
                                  ->with('licenses', $licenses)
                                  ->with('themes', $themes)
                                  ->with('publishers', $publishers)
                                  ->with('definitions', $definitions);

        return \Response::make($view);
    }
}

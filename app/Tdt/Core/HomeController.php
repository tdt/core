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

        // Polyfill
        if (! function_exists('array_column')) {
            function array_column($array, $column_name) {
                return array_map(function ($element) use ($column_name) {
                    return $element[$column_name];
                }, $array);
            }
        }

        // Get unique properties
        $keywords = Definitions\KeywordController::getKeywordList($definitions);
        $languages = array_count_values(array_filter(array_column($definitions->toArray(), 'language')));
        $licenses = array_count_values(array_filter(array_column($definitions->toArray(), 'rights')));
        $themes = array_count_values(array_filter(array_column($definitions->toArray(), 'theme')));
        $publishers = array_count_values(array_filter(array_column($definitions->toArray(), 'publisher_name')));

        // Sort by "Popularity"
        // For alphabetical order: use ksort
        arsort($keywords);
        arsort($languages);
        arsort($licenses);
        arsort($themes);
        arsort($publishers);

        $view = \View::make('home')->with('title', 'Datasets | The Datatank')
                                  ->with('page_title', 'Datasets')
                                  ->with('keywords', $keywords)
                                  ->with('languages', $languages)
                                  ->with('licenses', $licenses)
                                  ->with('themes', $themes)
                                  ->with('publishers', $publishers)
                                  ->with('definitions', $definitions);

        return \Response::make($view);
    }
}

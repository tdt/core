<?php

namespace Tdt\Core\Definitions;

use Illuminate\Routing\Router;
use Tdt\Core\Auth\Auth;
use Tdt\Core\Datasets\Data;
use Tdt\Core\ContentNegotiator;
use Tdt\Core\Pager;
use Tdt\Core\ApiController;
use Tdt\Core\Definitions\KeywordController;
use Illuminate\Support\Facades\Lang;

/**
 * InfoController: Controller that handles info requests and returns informational data about the datatank.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class InfoController extends ApiController
{
    public function get($uri)
    {
        // Set permission
        Auth::requirePermissions('info.view');

        // Split for an (optional) extension
        preg_match('/([^\.]*)(?:\.(.*))?$/', $uri, $matches);

        // URI is always the first match
        $uri = $matches[1];

        return $this->getInfo($uri);
    }

    /**
     * Return the headers of a call made to the uri given.
     */
    public function head($uri)
    {

        if (!empty($uri)) {
            if (!$this->definition->exists($uri)) {
                \App::abort(404, "No resource has been found with the uri $uri");
            }
        }

        $response =  \Response::make(null, 200);

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');
        $response->header('Pragma', 'public');

        // Return formatted response
        return $response;
    }

    /*
     * GET an info document based on the uri provided
     */
    private function getInfo($uri = null)
    {
        if (!empty($uri)) {
            if (!$this->definition->exists($uri)) {
                \App::abort(404, "No resource was found identified with " . $uri);
            }

            $description = $this->definition->getDescriptionInfo($uri);

            $result = new Data();
            $result->data = $description;

            return ContentNegotiator::getResponse($result, 'json');
        }

        $filters = ['keywords', 'rights', 'theme', 'language', 'publisher_name'];

        $filter_map = [];

        foreach ($filters as $filter) {
            $filter_values = $this->parseFilterValues(\Input::get($filter));

            if (!empty($filter_values)) {
                $filter_map[$filter] = $filter_values;
            }
        }

        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $definitions_info = $this->definition->getFiltered($filter_map, $limit, $offset);
        $definition_count = $this->definition->countFiltered($filter_map, $limit, $offset);

        // Polyfill support for PHP < 5.5
        if (!function_exists('array_column')) {
            function array_column($array, $column_name) {
                return array_map(function ($element) use ($column_name) {
                    return $element[$column_name];
                }, $array);
            }
        }

        // Get unique properties
        $theme = array_count_values(array_filter(array_column($definitions_info, 'theme')));
        $language = array_count_values(array_filter(array_column($definitions_info, 'language')));
        $rights = array_count_values(array_filter(array_column($definitions_info, 'rights')));
        $publisher = array_count_values(array_filter(array_column($definitions_info, 'publisher_name')));

        $keyword_strings = array_filter(array_column($definitions_info, 'keywords'));
        $all_keywords = [];

        foreach ($keyword_strings as $keyword_string) {
            $all_keywords = array_merge($all_keywords, explode(',', $keyword_string));
        }

        $keywords = array_count_values(array_map('trim', $all_keywords));

        // Sort by frequency
        arsort($theme);
        arsort($keywords);
        arsort($language);
        arsort($rights);
        arsort($publisher);

        $result = new Data();
        $result->paging = Pager::calculatePagingHeaders($limit, $offset, $definition_count);
        $result->data = [
            'filter' => [
                [
                 'filterProperty' => 'theme',
                 'displayName' => Lang::get('datasets.theme'), 'options' => $theme,
                 'count' => count($theme)
                ],
                [
                 'filterProperty' => 'keywords',
                 'displayName' => Lang::get('datasets.keywords'), 'options' => $keywords,
                 'count' => count($keywords)
                ],
                [
                 'filterProperty' => 'language',
                 'displayName' => Lang::get('datasets.language'), 'options' => $language,
                 'count' => count($language)
                ],
                [
                 'filterProperty' => 'rights',
                 'displayName' => Lang::get('datasets.license'), 'options' => $rights,
                 'count' => count($rights)
                ],
                [
                 'filterProperty' => 'publisher_name',
                 'displayName' => Lang::get('datasets.publisher'), 'options' => $publisher,
                 'count' => count($publisher)],
            ],
            'paging' => $result->paging,
            'datasets' => $definitions_info,
        ];

        return ContentNegotiator::getResponse($result, 'json');
    }

    private function parseFilterValues($value_str)
    {
        $filter_values = [];

        $all_values = explode(',', $value_str);

        foreach ($all_values as $filter_val) {
            $filter_val = trim($filter_val);

            if (!in_array($filter_val, $filter_values) && !empty($filter_val)) {
                $filter_values[] = $filter_val;
            }
        }

        return $filter_values;
    }

    /**
     * Return the response with the given data (formatted in json)
     */
    private function makeResponse($data)
    {
         // Create response
        $response = \Response::make(str_replace('\/', '/', json_encode($data)));

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

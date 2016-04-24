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

        $filters = ['keywords', 'rights', 'theme', 'language', 'publisher_name', 'query'];

        $filter_map = [];

        foreach ($filters as $filter) {
            $filter_values = $this->parseFilterValues(\Input::get($filter));

            if (!empty($filter_values)) {
                $filter_map[$filter] = $filter_values;
            }
        }

        list($limit, $offset) = Pager::calculateLimitAndOffset(50);

        $definitions_info = $this->definition->getFiltered($filter_map, $limit, $offset);
        $definition_count = $this->definition->countFiltered($filter_map, $limit, $offset);

        $facet_count = $this->definition->countFacets($filter_map);

        $facet_map = [];

        foreach ($facet_count as $facet) {
            if (empty($facet_map[$facet->facet_name])) {
                $facet_map[$facet->facet_name] = [];
            }

            $facet_map[$facet->facet_name][$facet->value] = $facet->count;
        }

        $result = new Data();

        $result->paging = Pager::calculatePagingHeaders($limit, $offset, $definition_count);

        $result->data = [
            'filter' => [
                [
                 'filterProperty' => 'theme',
                 'displayName' => Lang::get('datasets.theme'), 'options' => @$facet_map['theme'],
                 'count' => count(@$facet_map['theme'])
                ],
                [
                 'filterProperty' => 'keywords',
                 'displayName' => Lang::get('datasets.keywords'), 'options' => @$facet_map['keyword'],
                 'count' => count(@$facet_map['keyword'])
                ],
                [
                 'filterProperty' => 'language',
                 'displayName' => Lang::get('datasets.language'), 'options' => @$facet_map['language'],
                 'count' => count(@$facet_map['language'])
                ],
                [
                 'filterProperty' => 'rights',
                 'displayName' => Lang::get('datasets.license'), 'options' => @$facet_map['rights'],
                 'count' => count(@$facet_map['rights'])
                ],
                [
                 'filterProperty' => 'publisher_name',
                 'displayName' => Lang::get('datasets.publisher'), 'options' => @$facet_map['publisher_name'],
                 'count' => count(@$facet_map['publisher_name']),
                ]
            ],
            'paging' => $this->calculatePagingInfo($limit, $offset, $definition_count),
            'datasets' => $definitions_info,
        ];

        return ContentNegotiator::getResponse($result, 'json');
    }

    private function calculatePagingInfo($limit, $offset, $count)
    {
        $paging_info = Pager::calculatePagingHeaders($limit, $offset, $count);

        $paging = [
            'current' => ceil($offset / $limit) + 1,
            'first' => 1,
            'last' => ceil($count / $limit),
            'limit' => $limit,
            'offset' => $offset,
            'total' => $count
        ];

        if (!empty($paging_info['next'])) {
            $paging['next'] = $paging['current'] + 1;
        }

        if (!empty($paging_info['previous'])) {
            $paging['previous'] = $paging['current'] - 1;
        }

        return $paging;
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

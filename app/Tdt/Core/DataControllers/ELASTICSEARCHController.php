<?php namespace Tdt\Core\Datacontrollers;

use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use Elasticsearch\Client;

/**
 * Elasticsearch controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class ELASTICSEARCHController extends ADataController
{
    public function readData($source_definition, $rest_parameters = [])
    {
        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $query_param = \Input::get('query', '*');

        // Check for authentication
        if (!empty($source_definition['username']) && !empty($source_definition['password'])) {
            $auth = $source_definition['username'] . ':' . $source_definition['password'] . '@';

            $parts = parse_url($source_definition['host']);

            if ($parts['scheme'] == 'https') {
                $schemeless_url = str_replace('https://', '', $source_definition['host']);
                $source_definition['host'] = 'https://' . $auth . $schemeless_url;
            } else {
                $schemeless_url = str_replace('http://', '', $source_definition['host']);
                $source_definition['host'] = 'http://' . $auth . $schemeless_url;
            }
        }

        $hosts = ['hosts' => [$source_definition['host'] . ':' . $source_definition['port']]];
        $client = new Client($hosts);

        $search_params = [];
        $search_params['index'] = $source_definition['es_index'];
        $search_params['type'] = $source_definition['es_type'];
        $search_params['body']['query']['query_string']['query'] = $query_param;

        $results = $client->search($search_params);
        $data = [];
        $data_result = new Data();

        if (!empty($results['hits']['total'])) {
            $paging = Pager::calculatePagingHeaders($limit, $offset, $results['hits']['total']);

            $filtered_hits = [];

            foreach ($results['hits']['hits'] as $hit) {
                $filtered_hits[] = $hit['_source'];
            }

            $data_result->data = $filtered_hits;
        } else {
            $data_result->data = [];
            $data_result->paging = [];
        }

        $data_result->preferred_formats = $this->getPreferredFormats();

        return $data_result;
    }

    public static function getParameters()
    {
        $query_params = [
            "query" => [
                "required" => false,
                "description" => "A value that will be used to perform a full text-search on the data."
            ]
        ];

        return array_merge($query_params, parent::getParameters());
    }
}

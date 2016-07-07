<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use Elastica\Client;
use Elastica\Document;
use Elastica\Query\Term;
use Elastica\Search;
use Elastica\Query;
use Elastica\Exception\ResponseException;
use Elastica\Query\SimpleQueryString;
use Elastica\Query\MatchAll;

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
        Pager::setDefaultLimit(500);

        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $client = new Client([
            'host' => $source_definition['host'],
            'port' => $source_definition['port'],
            'username' => $source_definition['username'],
            'password' => $source_definition['password']
        ]);

        $index = $client->getIndex($source_definition['es_index']);
        $type = $index->getType($source_definition['es_type']);

        $search = new Search($client);
        $search->addIndex($index);
        $search->addType($type);

        $query_param = \Input::get('query');

        if (empty($query_param)) {
            $query = new MatchAll();
            $search->setQuery($query);
        } else {
            $query = new SimpleQueryString($query_param);
            $search->setQuery($query);
        }

        $search->getQuery()->setFrom($offset);
        $search->getQuery()->setSize($limit);

        $resultSet = $search->search();

        $data = new Data();
        $data_results = [];

        foreach ($resultSet->getResults() as $result) {
            $data_result = $result->getData();
            unset($data_result['__tdt_etl_timestamp__']);

            $data_results[] = $data_result;
        }

        $data->data = $data_results;

        if ($resultSet->getTotalHits() > 0) {
            $paging = Pager::calculatePagingHeaders($limit, $offset, $resultSet->getTotalHits());
            $data->paging = $paging;
        }

        $data->preferred_formats = $this->getPreferredFormats();

        return $data;
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

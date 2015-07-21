<?php namespace Tdt\Core\Datacontrollers;

use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use MongoClient;

/**
 * Mongo controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class MONGOController extends ADataController
{
    public function readData($source_definition, $rest_parameters = [])
    {
        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $collection = $this->getCollection($source_definition);

        $query = [];

        $total_objects = $collection->count($query);

        $cursor = $collection->find($query)->skip($offset)->limit($limit);

        $results = [];

        foreach ($cursor as $result) {
            unset($result['_id']);

            $results[] = $result;
        }

        $paging = Pager::calculatePagingHeaders($limit, $offset, $total_objects);

        $data_result = new Data();
        $data_result->data = $results;
        $data_result->paging = $paging;
        $data_result->preferred_formats = $this->getPreferredFormats();

        return $data_result;
    }

    /**
     * Create and return a MongoCollection
     *
     * @param array $source_definition The configuration for the mongo resource
     *
     * @return \MongoCollection
     */
    private function getCollection($source_definition)
    {
        $prefix = '';

        if (!empty($source_definition['username'])) {
            $prefix = $source_definition['username'] . $source_definition['password'];
        }

        $connString = 'mongodb://' . $prefix . $source_definition['host'] . ':' . $source_definition['port'];

        try {
            $client = new MongoClient($connString);
        } catch (\MongoConnectionException $ex) {
            \App::abort(500, 'Could not create a connection with the MongoDB, please check if the configuration is still ok.');
        }

        $mongoCollection = $client->selectCollection($source_definition['database'], $source_definition['mongo_collection']);

        return $mongoCollection;
    }
}

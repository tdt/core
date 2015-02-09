<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\RmlLogRepositoryInterface;

/**
 * The Rml Log Repository class implemented for a mongodb database
 *
 * @author Jan Vansteenlandt
 */
class RmlLogRepository implements RmlLogRepositoryInterface
{
    /**
     * Get all of the rml job logs for an identifier
     *
     * @param string  $identifier The identifier of the job
     * @param integer $limit      The limit of the amount of logs
     * @param integer $offset     The offset of the results
     *
     * @return array
     */
    public function get($identifier, $limit = 5000, $offset = 0)
    {
        $collection = $this->getMongoCollection();

        $cursor = $collection->find(
            array(
                'identifier' => $identifier
            ),
            array(
                '_id' => 0
            )
        );

        $logs = array();

        while ($cursor->hasNext()) {
            array_push($logs, $cursor->getNext());
        }


        return $logs;
    }

    /**
     * Delete all rml job logs for a given identifier
     *
     * @param string $identifier The identifier of the job
     *
     * @return void
     */
    public function delete($identifier)
    {
        $collection = $this->getMongoCollection();

        $result = $collection->remove(
            array(
                'identifier' => $identifier
            )
        );

        if ($result) {
            \Log::info('Removed RML job logs for the identifier ' . $identifier);
        } else {
            \Log::info('No RML job logs removed, it was either not a job, or no log records have been removed/found.');
        }
    }

    /**
     * Insert new log for an rml job
     *
     * @param string $identifier The identifier of the job
     * @param array  $log        The document to be added to the log collection
     *
     * @return bool
     */
    public function insert($identifier, $log)
    {
        $collection = $this->getMongoCollection();

        $result = $collection->insert($log);

        if (!empty($result['err'])) {

            \Log::error('Something went wrong while inserting a log for the rml job with identifier ' . $identifier);

            return $false;
        }

        return true;
    }

    /**
     * Create and return the mongo collection
     * for rml jobs
     *
     * @return \MongoCollection
     */
    private function getMongoCollection()
    {
        $client = new \MongoClient(\Config::get('mongolog.rml_log.server'));

        return $client->selectCollection(\Config::get('mongolog.rml_log.database'), \Config::get('mongolog.rml_log.collection'));
    }
}

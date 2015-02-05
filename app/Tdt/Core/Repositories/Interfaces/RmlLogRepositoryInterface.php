<?php

namespace Tdt\Core\Repositories\Interfaces;

/**
 * The interface of the Rml Log Repository
 *
 * @author Jan Vansteenlandt
 */
interface RmlLogRepositoryInterface
{
    /**
     * Get all of the rml job logs for a job
     *
     * @param string  $identifier The identifier of the job
     * @param integer $limit      The limit of the amount of logs
     * @param integer $offset     The offset of the results
     *
     * @return array
     */
    public function get($identifier, $limit, $offset);

    /**
     * Delete all rml job logs for a job
     *
     * @param string $identifier The identifier of the job
     *
     * @return bool
     */
    public function delete($identifier);

    /**
     * Insert new log for an rml job
     *
     * @param string $identifier The identifier of the job
     * @param array  $log        The document to be added to the log collection
     *
     * @return bool
     */
    public function insert($identifier, $log);
}

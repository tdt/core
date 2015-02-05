<?php

namespace Tdt\Core\Definitions;

use Tdt\Core\ApiController;
use Tdt\Core\Repositories\Interfaces\RmlLogRepositoryInterface;

/**
 * Class that handles get requests for rml job logs
 *
 * @author Jan Vansteenlandt
 */
class RmlLogController extends ApiController
{
    public function __construct(RmlLogRepositoryInterface $logs)
    {
        $this->logs = $logs;
    }

    /**
     * Get the RML job logs for a given identifier
     *
     * @param string $identifier
     *
     * @return array
     */
    public function get($identifier)
    {
        $logs = $this->logs->get($identifier);

        $response = \Response::make($logs, 200);

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');
        $response->header('Pragma', 'public');

        // Return response
        return $response;
    }
}

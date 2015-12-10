<?php

namespace Tdt\Core\Definitions;

use Illuminate\Routing\Router;

use Tdt\Core\Auth\Auth;
use Tdt\Core\ApiController;
use Tdt\Core\Repositories\Interfaces\GeoprojectionRepositoryInterface;

/**
 * Geoprojection controller: handles request to handle geo projections
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class GeoprojectionController extends ApiController
{

    private $geoprojections;

    public function __construct(GeoprojectionRepositoryInterface $geoprojections)
    {
        $this->geoprojections = $geoprojections;
    }

    public function get($uri)
    {
        // Set permission
        Auth::requirePermissions('info.view');

        return $this->getProjections($uri);
    }

    /**
     * Return the headers of a call made to the uri given.
     */
    public function head($uri)
    {
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
    private function getProjections($uri)
    {
        return $this->makeResponse($this->geoprojections->getAll());
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

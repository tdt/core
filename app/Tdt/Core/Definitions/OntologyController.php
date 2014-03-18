<?php

namespace Tdt\Core\Definitions;

use Illuminate\Routing\Router;
use Tdt\Core\Auth\Auth;
use Tdt\Core\ApiController;
use Tdt\Core\Repositories\Interfaces\OntologyRepositoryInterface;

/**
 * OntologyController: Controller that handels the available ontologies and prefixes available for semantic data results
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class OntologyController extends ApiController
{

    private $ontologies;

    public function __construct(OntologyRepositoryInterface $ontologies)
    {
        $this->ontologies = $ontologies;
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
    public function get($uri)
    {
        // Set permission
        Auth::requirePermissions('info.view');

        $ontologies = $this->ontologies->getAll();

        return $this->makeResponse($ontologies);
    }

    /**
     * Return the response with the given data (formatted in json)
     */
    private function makeResponse($data)
    {

         // Create response
        $response = \Response::make(str_replace('\/','/', json_encode($data)));

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

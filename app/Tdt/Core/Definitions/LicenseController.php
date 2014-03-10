<?php

namespace Tdt\Core\Definitions;

use Illuminate\Routing\Router;
use Tdt\Core\Auth\Auth;
use Tdt\Core\ApiController;

/**
 * LicenseController: Controller that handels the available licenses for the DCAT
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class LicenseController extends ApiController
{

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

        // Fetch the columns that can be shown in the result
        $license_repository = \App::make('Tdt\\Core\\Repositories\\Interfaces\\LicenseRepositoryInterface');

        $licenses = $license_repository->getAll();

        return $this->makeResponse($licenses);
    }

    /**
     * Return the response with the given data (formatted in json)
     */
    private function makeResponse($data)
    {

         // Create response
        $response = \Response::make(str_replace('\/','/', json_encode($data, true)));

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

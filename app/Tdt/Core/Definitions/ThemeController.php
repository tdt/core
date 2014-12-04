<?php

namespace Tdt\Core\Definitions;

use Illuminate\Routing\Router;
use Tdt\Core\Auth\Auth;
use Tdt\Core\ApiController;
use Tdt\Core\Repositories\Interfaces\ThemeRepositoryInterface;

/**
 * ThemeController: Controller that handels the available themes for the DCAT vocabulary
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class ThemeController extends ApiController
{

    private $themes;

    public function __construct(ThemeRepositoryInterface $themes)
    {
        $this->themes = $themes;
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

        return $this->makeResponse($this->themes->getAll());
    }

    /**
     * Return the response with the given data (formatted in json)
     */
    private function makeResponse($data)
    {
         // Create response
        $response = \Response::make(str_replace('\/', '/', json_encode($data, true)));

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

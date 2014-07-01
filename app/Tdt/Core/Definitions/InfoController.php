<?php

namespace Tdt\Core\Definitions;

use Illuminate\Routing\Router;
use Tdt\Core\Auth\Auth;
use Tdt\Core\Datasets\Data;
use Tdt\Core\ContentNegotiator;
use Tdt\Core\Pager;
use Tdt\Core\ApiController;

/**
 * InfoController: Controller that handles info requests and returns informational data about the datatank.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class InfoController extends ApiController
{

    public function get($uri)
    {

        // Set permission
        Auth::requirePermissions('info.view');

        // Split for an (optional) extension
        preg_match('/([^\.]*)(?:\.(.*))?$/', $uri, $matches);

        // URI is always the first match
        $uri = $matches[1];

        return $this->getInfo($uri);
    }

    /**
     * Return the headers of a call made to the uri given.
     */
    public function head($uri)
    {

        if (!empty($uri)) {
            if (!$this->definition->exists($uri)) {
                \App::abort(404, "No resource has been found with the uri $uri");
            }
        }

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
    private function getInfo($uri = null)
    {
        if (!empty($uri)) {

            if (!$this->definition->exists($uri)) {
                \App::abort(404, "No resource was found identified with " . $uri);
            }

            $description = $this->definition->getDescriptionInfo($uri);

            $result = new Data();
            $result->data = $description;

            return ContentNegotiator::getResponse($result, 'json');
        }

        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $definitions_info = $this->definition->getAllDefinitionInfo($limit, $offset);

        $definition_count = $this->definition->countPublished();

        $result = new Data();
        $result->paging = Pager::calculatePagingHeaders($limit, $offset, $definition_count);
        $result->data = $definitions_info;

        return ContentNegotiator::getResponse($result, 'json');
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

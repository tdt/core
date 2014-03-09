<?php

namespace tdt\core\definitions;

use Illuminate\Routing\Router;
use tdt\core\auth\Auth;
use tdt\core\datasets\Data;
use tdt\core\Pager;
use tdt\core\ContentNegotiator;
use repositories\DefinitionRepositoryInterface;
use tdt\core\ApiController;

/**
 * DefinitionController
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DefinitionController extends ApiController
{

    protected $definition;

    public function __construct(\repositories\interfaces\DefinitionRepositoryInterface $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Create a new definition based on the PUT parameters given and content-type
     */
    public function put($uri)
    {

        // Check for the correct content type header if set
        if (!empty($content_type) && $content_type != 'application/tdt.definition+json') {
            \App::abort(400, "The content-type header with value ($content_type) was not recognized.");
        }

        $input = $this->fetchInput();

        // Add the collection and uri to the input
        preg_match('/(.*)\/([^\/]*)$/', $uri, $matches);

        $input['collection_uri'] = @$matches[1];
        $input['resource_name'] = @$matches[2];

        // Validate the input
        $validator = $this->definition->getValidator($input);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, "Something went wrong during validation, the message we got is: " . $message);
        }

        // Create the new definition
        $definition = $this->definition->store($input);

        $response = \Response::make(null, 200);
        $response->header('Location', \URL::to($definition['collection_uri'] . '/' . $definition['resource_name']));

        return $response;
    }

    /**
     * Delete a definition based on the URI given.
     */
    public function delete($uri)
    {

        $this->definition->delete($uri);

        return \Response::make(null, 200);
    }

    /**
     * PATCH a definition based on the PATCH parameters and URI.
     */
    public function patch($uri)
    {

        // Check for the correct content type header if set
        if (!empty($content_type) && $content_type != 'application/tdt.definition+json') {
            \App::abort(400, "The content-type header with value ($content_type) was not recognized.");
        }

        $input = $this->fetchInput();

        // Add the collection and uri to the input
        preg_match('/(.*)\/([^\/]*)$/', $uri, $matches);

        $input['collection_uri'] = @$matches[1];
        $input['resource_name'] = @$matches[2];

        // Validate the input
        $validator = $this->definition->getValidator($input);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, "Something went wrong during validation, the message we got is: " . $message);
        }

        $this->definition->update($uri, $input);

        $response = \Response::make(null, 200);

        return $response;
    }

    /**
     * Return the headers of a call made to the uri given.
     */
    public function head($uri)
    {

        if ($this->definition->exists($uri)) {
            \App::abort(404, "No resource has been found with the uri $uri");
        }

        $response =  \Response::make(null, 200);

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');
        $response->header('Pragma', 'public');

        // Return formatted response
        return $response;
    }

    /*
     * GET a definition based on the uri provided
     */
    public function get($uri)
    {

        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $definitions = $this->definition->getAllFullDescriptions($uri, $limit, $offset);

        $definition_count = $this->definition->count();

        $result = new Data();
        $result->paging = Pager::calculatePagingHeaders($limit, $offset, $definition_count);
        $result->data = $definitions;

        return ContentNegotiator::getResponse($result, 'json');
    }

    /**
     * Retrieve the input
     */
    private function fetchInput()
    {

        // Retrieve the parameters of the PUT requests (either a JSON document or a key=value string)
        $input = \Request::getContent();

        // Is the body passed as JSON, if not try getting the request parameters from the uri
        if (!empty($input)) {
            $input = json_decode($input, true);
        } else {
            $input = \Input::all();
        }

        // If input is empty, then something went wrong
        if (empty($input)) {
            \App::abort(400, "The parameters could not be parsed from the body or request URI, make sure parameters are provided and if they are correct (e.g. correct JSON).");
        }

        // Change all of the parameters to lowercase
        $input = array_change_key_case($input);

        return $input;
    }

    /**
     * Return the response with the given data (formatted in json)
     */
    public function makeResponse($data)
    {

         // Create response
        $response = \Response::make($data, 200);

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

<?php

namespace Tdt\Core\Definitions;

use Tdt\Core\Auth\Auth;
use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use Tdt\Core\ContentNegotiator;
use Tdt\Core\ApiController;
use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;
use Config;

/**
 * DefinitionController
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DefinitionController extends ApiController
{
    protected $definitions;

    public function __construct(DefinitionRepositoryInterface $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * Create and Link Job (elasticsearch): Get the class without the namespace
     */
    private function getClass($obj)
    {
        if (is_null($obj)) {
            return null;
        }

        $class_pieces = explode('\\', get_class($obj));
        $class = ucfirst(mb_strtolower(array_pop($class_pieces)));

        return implode('\\', $class_pieces) . '\\' . $class;
    }

    /**
     * Create and Link Job (elasticsearch): Validate the create parameters based on the rules of a certain job.
     * If something goes wrong, abort the application and return a corresponding error message.
     *
     * @param string $type
     * @param string $short_name
     * @param array  $params
     *
     * @return array
     */
    private function validateParameters($type, $short_name, $params)
    {
        $validated_params = array();

        $create_params = $type::getCreateProperties();
        $rules = $type::getCreateValidators();

        foreach ($create_params as $key => $info) {
            if (! array_key_exists($key, $params)) {
                if (! empty($info['required']) && $info['required']) {
                    if (strtolower($type) != 'job') {
                        \App::abort(
                            400,
                            "The parameter '$key' of the $short_name-part of the job configuration is required but was not passed."
                        );
                    } else {
                        \App::abort(400, "The parameter '$key' is required to create a job but was not passed.");
                    }
                }

                $validated_params[$key] = @$info['default_value'];

            } else {
                if (! empty($rules[$key])) {
                    $validator = \Validator::make(
                        array($key => $params[$key]),
                        array($key => $rules[$key])
                    );

                    if ($validator->fails()) {
                        \App::abort(
                            400,
                            "The validation failed for parameter $key with value '$params[$key]', make sure the value is valid."
                        );
                    }
                }

                $validated_params[$key] = $params[$key];
            }
        }

        return $validated_params;
    }

    /**
     * Create and Link Job (elasticsearch): Check if a given type of the ETL exists.
     */
    private function getClassOfType($params, $ns)
    {
        $type = @$params['type'];
        $type = ucfirst(mb_strtolower($type));

        $class_name = $ns . '\\' . $type;

        if (! class_exists($class_name)) {
            \App::abort(400, "The given type ($type) is not a $ns type.");
        }

        $class = new $class_name();

        // Validate the properties of the given type
        $validated_params = $this->validateParameters($class, $type, $params);

        foreach ($validated_params as $key => $value) {
            $class->$key = $value;
        }

        return $class;
    }

    /**
     * Create and and return the job linked to the new definition
     *
     * @param string $uri   The URI of the datasource
     * @param array  $input The input of the request
     *
     * @return integer The ID of the job
     */
    public function createLinkJob($uri, $input)
    {
        // Set permission
        Auth::requirePermissions('definition.create');

        preg_match('/(.*)\/([^\/]*)$/', $uri, $matches);

        $collection_uri = @$matches[1];
        $name = @$matches[2];

        // Extract class construction
        $params = [];
        $params['extract']['type'] = $input['original-dataset-type'];
        $params['extract']['uri'] = $input['uri'];

        if ($params['extract']['type'] == 'csv') {
            $params['extract']['delimiter'] = $input['delimiter'];
            $params['extract']['has_header_row'] = $input['has_header_row'];
            $params['extract']['encoding'] = 'UTF-8';
        } elseif ($params['extract']['type'] == 'xml') {
            $params['extract']['array_level'] = $input['array_level'];
            $params['extract']['encoding'] = 'UTF-8';
        }

        // Load class construction (always elasticsearch)
        $params['load']['type'] = 'elasticsearch';
        $params['load']['host'] = \Config::get('database.connections.tdt_elasticsearch.host', 'localhost');
        $params['load']['port'] = \Config::get('database.connections.tdt_elasticsearch.port', 9200);
        $params['load']['es_index'] = \Config::get('database.connections.tdt_elasticsearch.index', 'datatank');
        $params['load']['es_type'] = trim($collection_uri) . '_' . trim($name);
        $params['load']['username'] = $input['username'];
        $params['load']['password'] = $input['password'];

        // Add schedule
        $params['schedule'] = $input['schedule'];

        // Validate the job properties
        $job_params = $this->validateParameters('Job', 'job', $params);

        $extract = @$params['extract'];
        $load = @$params['load'];

        // Check for every emlp part if the type is supported
        $extractor = $this->getClassOfType(@$extract, 'Extract');
        $loader = $this->getClassOfType(@$load, 'Load');

        // Save the emlp models
        $extractor->save();
        $loader->save();

        // Create the job associated with emlp relations
        $job = new \Job();
        $job->collection_uri = $collection_uri;
        $job->name = $name;

        // Add the validated job params
        foreach ($job_params as $key => $value) {
            $job->$key = $value;
        }

        $job->extractor_id = $extractor->id;
        $job->extractor_type = $this->getClass($extractor);

        $job->loader_id = $loader->id;
        $job->loader_type = $this->getClass($loader);
        $job->save();

        // Execute the job for a first time
        $job->date_executed = time();
        $job->save();

        $job_name = $job->collection_uri . '/' . $job->name;

        \Queue::push(function ($queued_job) use ($job_name) {
            \Artisan::call('input:execute', [
                'jobname' => $job_name
            ]);

            $queued_job->delete();
        });

        return $job->id;
    }

    /**
     * Create a new definition based on the PUT parameters given and content-type
     */
    public function put($uri)
    {
        // Set permission
        Auth::requirePermissions('definition.create');

        // Check for the correct content type header if set
        if (! empty($content_type) && $content_type != 'application/tdt.definition+json') {
            \App::abort(400, "The content-type header with value ($content_type) was not recognized.");
        }

        $input = $this->fetchInput();

        $input['original-dataset-type'] = $input['type'];

        // Add the collection and uri to the input
        preg_match('/(.*)\/([^\/]*)$/', $uri, $matches);

        $input['collection_uri'] = @$matches[1];
        $input['resource_name'] = @$matches[2];

        // Add uploaded file and change uri.
        // TODO: Validate file extension based on selected dataset/definition.
        if (isset($input['fileupload']) && $input['fileupload'] != '') {
            $input['uri'] = 'file://' . $input['fileupload'];
        }

        // Check if dataset should be indexed
        if (isset($input['to_be_indexed']) && $input['to_be_indexed'] == 1) {
            $input['type'] = 'elasticsearch';
            $input['es_type'] = trim($input['collection_uri']) . '_' . trim($input['resource_name']);
            $input['host'] = \Config::get('database.connections.tdt_elasticsearch.host', 'localhost');
            $input['port'] = \Config::get('database.connections.tdt_elasticsearch.port', 9200);
            $input['es_index'] = \Config::get('database.connections.tdt_elasticsearch.index', 'datatank');
        }

        // Validate the input
        $validator = $this->definitions->getValidator($input);

        if ($validator->fails()) {
            $message = $validator->messages()->first();

            \App::abort(400, $message);
        }

        // Create the new definition
        $definition = $this->definitions->store($input);

        // Check if dataset should be indexed: create job and link with previously created definition.
        if (isset($input['to_be_indexed']) && $input['to_be_indexed'] == 1) {
            // Create new job
            $job_id = $this->createLinkJob($uri, $input);

            // Link job with definition through job_id column.
            $input['job_id'] = $job_id;
            $definition = $this->definitions->update($uri, $input); // update previously created definition
        }

        $response = \Response::make(null, 200);
        $response->header(
            'Location',
            \URL::to(
                $definition['collection_uri'] . '/' . $definition['resource_name'],
                [],
                Config::get('app.ssl_enabled')
            )
        );

        return $response;
    }

    /**
     * Delete a definition based on the URI given.
     */
    public function delete($uri)
    {
        // Set permission
        Auth::requirePermissions('definition.delete');

        $this->definitions->delete($uri);

        return \Response::make(null, 200);
    }

    /**
     * PATCH a definition based on the PATCH parameters and URI.
     */
    public function patch($uri)
    {
        // Set permission
        Auth::requirePermissions('definition.update');

        // Check for the correct content type header if set
        if (! empty($content_type) && $content_type != 'application/tdt.definition+json') {
            \App::abort(400, "The content-type header with value ($content_type) was not recognized.");
        }

        $input = $this->fetchInput();

        // Add the collection and uri to the input
        preg_match('/(.*)\/([^\/]*)$/', $uri, $matches);

        $input['collection_uri'] = @$matches[1];
        $input['resource_name'] = @$matches[2];

        // Add uploaded file and change uri.
        // TODO: Validate file extension based on selected dataset/definition.
        if(isset($input['fileupload']) && $input['fileupload'] != '') {
            $input['uri'] = 'file://' . $input['fileupload'];
        }

        // Validate the input
        $validator = $this->definitions->getValidator($input);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, $message);
        }

        $this->definitions->update($uri, $input);

        $response = \Response::make(null, 200);

        return $response;
    }

    /**
     * Return the headers of a call made to the uri given.
     */
    public function head($uri)
    {
        // Set permission
        Auth::requirePermissions('definition.view');

        if ($this->definitions->exists($uri)) {
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
        // Set permission
        Auth::requirePermissions('definition.view');

        if (! empty($uri)) {
            if (! $this->definitions->exists($uri)) {
                \App::abort(404, 'No resource was found identified with ' . $uri);
            }

            $description = $this->definitions->getFullDescription($uri);

            $result = new Data();
            $result->data = $description;

            return ContentNegotiator::getResponse($result, 'json');
        }

        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $definitions = $this->definitions->getAllFullDescriptions($limit, $offset);

        $definition_count = $this->definitions->count();

        $result = new Data();
        $result->paging = Pager::calculatePagingHeaders($limit, $offset, $definition_count);
        $result->data = $definitions;

        return ContentNegotiator::getResponse($result, 'json');
    }

    /**
     * Retrieve the input, make sure all keys are lowercased
     */
    private function fetchInput()
    {

        // Retrieve the parameters of the PUT requests (either a JSON document or a key=value string)
        $input = \Request::getContent();

        // Is the body passed as JSON, if not try getting the request parameters from the uri
        if (! empty($input)) {
            $input = json_decode($input, true);
        } else {
            $input = \Input::all();
        }

        // If input is empty, then something went wrong
        if (empty($input)) {
            \App::abort(400, 'The parameters could not be parsed from the body or request URI, make sure parameters are provided and if they are correct (e.g. correct JSON).');
        }

        // Change all of the parameters to lowercase
        $input = array_change_key_case($input);

        return $input;
    }
}

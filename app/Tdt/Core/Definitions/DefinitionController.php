<?php

namespace Tdt\Core\Definitions;

use Tdt\Core\Auth\Auth;
use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use Tdt\Core\ContentNegotiator;
use Tdt\Core\ApiController;
use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;
use Config;
use File;
use ZipArchive;

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
     * Return a class without the namespace
     *
     * @return string
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
        } elseif ($params['extract']['type'] == 'xls') {
            $params['extract']['has_header_row'] = $input['has_header_row'];
            $params['extract']['start_row'] = $input['start_row'];
            $params['extract']['sheet'] = $input['sheet'];
        }

        // Load class construction (always elasticsearch)
        $params['load']['type'] = 'elasticsearch';
        $params['load']['host'] = \Config::get('database.connections.tdt_elasticsearch.host', 'localhost');
        $params['load']['port'] = \Config::get('database.connections.tdt_elasticsearch.port', 9200);
        $params['load']['es_index'] = \Config::get('database.connections.tdt_elasticsearch.index', 'datatank');
        $params['load']['es_type'] = str_replace(' ', '_', trim($collection_uri) . '_' . trim($name));
        $params['load']['username'] = \Config::get('database.connections.tdt_elasticsearch.username', '');
        $params['load']['password'] = \Config::get('database.connections.tdt_elasticsearch.password', '');

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

        $this->addJobToQueue($job_name, $uri);

        return $job->id;
    }

    /**
     * Edit job linked to dataset
     *
     * @return \Response
     */
    private function editLinkedJob($uri, $input)
    {
        // Set permission
        Auth::requirePermissions('definition.update');

        $job = \Job::whereRaw("? like CONCAT(collection_uri, '/', name , '/', '%')", array($uri . '/'))
            ->with('extractor', 'loader')->first();

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
        } elseif ($params['extract']['type'] == 'json') {
            /* No extra fields */
        }

        $params['load']['type'] = 'elasticsearch';
        $params['load']['host'] = \Config::get('database.connections.tdt_elasticsearch.host', 'localhost');
        $params['load']['port'] = \Config::get('database.connections.tdt_elasticsearch.port', 9200);
        $params['load']['es_index'] = \Config::get('database.connections.tdt_elasticsearch.index', 'datatank');
        $params['load']['es_type'] = str_replace(' ', '_', trim($collection_uri) . '_' . trim($name));
        $params['load']['username'] = \Config::get('database.connections.tdt_elasticsearch.username', '');
        $params['load']['password'] = \Config::get('database.connections.tdt_elasticsearch.password', '');

        // Add schedule
        $params['schedule'] = $job->schedule;

        // Validate the job properties
        $job_params = $this->validateParameters('Job', 'job', $params);

        // Check which parts are set for validation purposes
        $extract = @$params['extract'];
        $load = @$params['load'];

        // Check for every ETL part if the type is supported
        $extractor = $this->getClassOfType(@$extract, 'Extract');
        $loader = $this->getClassOfType(@$load, 'Load');

        $job->extractor()->delete();
        $job->loader()->delete();

        $extractor->save();
        $loader->save();

        // Add the validated job params
        foreach ($job_params as $key => $value) {
            $job->$key = $value;
        }

        $job->extractor_id = $extractor->id;
        $job->extractor_type = $this->getClass($extractor);

        $job->loader_id = $loader->id;
        $job->loader_type = $this->getClass($loader);
        $job->save();

        $job_name = $job->collection_uri . '/' . $job->name;

        // Push the job to the queue
        $this->addJobToQueue($job_name, $uri);

        $job->added_to_queue = true;
        $job->save();

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

        // Author user information
        $user = \Sentry::getUser();
        $input['user_id'] = $user->id;
        $input['username'] = $user->email;

        $input['original-dataset-type'] = $input['type'];

        // Add the collection and uri to the input
        preg_match('/(.*)\/([^\/]*)$/', $uri, $matches);

        $input['collection_uri'] = @$matches[1];
        $input['resource_name'] = @$matches[2];

        // Add uploaded file and change uri.
        if (isset($input['fileupload']) && $input['fileupload'] != '') {
            $input['uri'] = 'file://' . $input['fileupload'];
        }
      
        // Add uploaded file XSLT and change xslt_file.
        if (isset($input['fileupload_xslt']) && $input['fileupload_xslt'] != '') {

            $file2=$input['fileupload_xslt'];
            $file3=explode("\\", $file2);

            $input['xslt_file'] ='file://' . app_path() . '/storage/app/'. $file3[2] . '_' . date('Y-m-d') .'.xslt';
        }

        // Check if dataset should be indexed
        if (isset($input['to_be_indexed']) && $input['to_be_indexed'] == 1) {
            $input['es_type'] = $input['collection_uri'] . '_' . $input['resource_name'];

            // if a new job is stored and it needs to be indexed, set the draft flag to true
            $input['draft_flag'] = 1;
        }

        // Validate the input
        $validator = $this->definitions->getValidator($input);

        if ($validator->fails()) {
            $message = $validator->messages()->first();

            \App::abort(400, $message);
        }

        // Create the new definition
        $input = $this->processZip($input);
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
     * Check for any zip files as a URI for SHP data sources
     *
     * @param  array $input
     * @return array
     */
    private function processZip($input)
    {
        $datasetType = @$input['original-dataset-type'];

        if (empty($input['original-dataset-type'])) {
            $definition = \App::make('Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface')->getByIdentifier($input['collection_uri'] . '/' . $input['resource_name']);

            $datasetType = $definition['source_type'];
        }

        $datasetType = strtolower($datasetType);

        if ($datasetType == 'shp') {
            // Check for a zip file as a URI
            if (ends_with($input['uri'], '.zip')) {
                $uri = $input['uri'];
                $uri = str_replace('file://', '', $uri);

                $zip = new ZipArchive;
                $success = $zip->open($uri);

                if ($success === true) {
                    $path = storage_path() . '/app/' . str_random(5);

                    mkdir($path);

                    $zip->extractTo($path);
                    $zip->close();

                    // Get the shp file in the new directory
                    $files = scandir($path);
                    $shp_file = '';

                    foreach ($files as $file) {
                        if (strlen($file) > 4) {
                            chmod($path . '/' . $file, 0655);
                        }

                        if (ends_with($file, '.shp')) {
                            $shp_file = $file;
                        }
                    }

                    if (! empty($shp_file)) {
                        $input['uri'] = $path . '/' . $shp_file;
                    } else {
                        throw new \Exception('No shape file was found in the zip archive.');
                    }
                }
            }
        }

        return $input;
    }

    /**
     * Delete a definition based on the URI given.
     */
    public function delete($uri)
    {
        // Set permission
        Auth::requirePermissions('definition.delete');

        // Delete definition updates
        $definition = \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name , '/', '%')", array($uri . '/'))->with('location', 'attributions')->first();
        \DB::table('definitions_updates')->where('definition_id', $definition['id'])->delete();

        // Delete definition
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

        // Keep Author user information
        $definition = \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name , '/', '%')", array($uri . '/'))->with('location', 'attributions')->first();

        $input['user_id'] = $definition['user_id'];
        $input['username'] = $definition['username'];
        $input['xslt_file'] = $definition['xslt_file'];

        // Keep associated job
        $input['job_id'] = $definition['job_id'];

        // Add the collection and uri to the input
        preg_match('/(.*)\/([^\/]*)$/', $uri, $matches);

        $input['collection_uri'] = @$matches[1];
        $input['resource_name'] = @$matches[2];

        // Add uploaded file and change uri.
        // TODO: Validate file extension based on selected dataset/definition.
        if (isset($input['fileupload']) && $input['fileupload'] != '') {
            $input['uri'] = 'file://' . $input['fileupload'];
        }

        //Add uploaded xslt file
        if (isset($input['fileupload_xslt']) && $input['fileupload_xslt'] != '') {

            $file2=$input['fileupload_xslt'];
            $file3=explode("\\", $file2);

            $input['xslt_file'] ='file://' . app_path() . '/storage/app/' .$file3[2] . '_' . date('Y-m-d').'.xslt';
        }

        // Validate the input
        $validator = $this->definitions->getValidator($input);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, $message);
        }

        $input = $this->processZip($input);
        $this->definitions->update($uri, $input);

        // Dataset updates control
        $user = \Sentry::getUser();
        $definition = \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name , '/', '%')", array($uri . '/'))->with('location', 'attributions')->first();

        $id = \DB::table('definitions_updates')->insertGetId(
            array('definition_id' => $definition['id'], 'user_id' => $user->id, 'username' => $user->email, 'updated_at' => $definition['updated_at'])
        );

        // Check if dataset has a linked job (for updating purposes only if uri dataset field has been modified)
        if ($definition['job_id'] != null && isset($input['fileupload']) && $input['fileupload'] != '') {
            $input['original-dataset-type'] = strtolower(chop($definition['source_type'], 'Definition'));

            $job_id = $this->editLinkedJob($uri, $input);
        }

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

    /**
     * Execute a job for a definition
     *
     * @param  string $job_name
     * @param  string $definition_uri
     * @return void
     */
    private function addJobToQueue($job_name, $definition_uri)
    {
        $definitions = \App::make('Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface');

        $definition = $definitions->getByIdentifier($definition_uri);
        $definition['draft_flag'] = true;
        $definitions->update($definition_uri, $definition);

        \Queue::push(function ($queued_job) use ($job_name, $definition_uri, $definitions) {
            \Artisan::call('input:execute', [
                'jobname' => $job_name
            ]);

            $definition = $definitions->getByIdentifier($definition_uri);
            $definition['draft_flag'] = false;
            $definitions->update($definition_uri, $definition);

            $queued_job->delete();
        });
    }
}

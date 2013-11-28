<?php

namespace tdt\core\definitions;

use Illuminate\Routing\Router;
use tdt\core\auth\Auth;
use tdt\core\datasets\Data;


/**
 * DefinitionController
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DefinitionController extends \Controller {

    // Don't allow occupied prefixes: api, discovery
    private static $FORBIDDEN_PREFIX = array('api', 'discovery');

    public static function handle($uri){

        $uri = ltrim($uri, '/');

        // Propagate the request based on the HTTPMethod of the request
        $method = \Request::getMethod();

        switch($method){
            case "PUT":

                // Set permission
                Auth::requirePermissions('definition.create');

                return self::createDefinition($uri);
                break;
            case "GET":
                // Set permission
                Auth::requirePermissions('definition.view');

                return self::viewDefinition($uri);
                break;
            case "POST":
            case "PATCH":

                // Set permission
                Auth::requirePermissions('definition.update');
                return self::updateDefinition($uri);
                break;
            case "DELETE":
                // Set permission
                Auth::requirePermissions('definition.delete');

                return self::deleteDefinition($uri);
                break;
            case "HEAD":
                // Set permission
                Auth::requirePermissions('definition.view');

                return self::headDefinition($uri);
                break;
            default:
                // Method not supported
                \App::abort(405, "The HTTP method '$method' is not supported by this resource.");
                break;
        }
    }

    /**
     * Create a new definition based on the PUT parameters given and content-type.
     */
    private static function createDefinition($uri){

        // Check if the uri already exists
        if(self::exists($uri)){
            self::deleteDefinition($uri);
        }

        // Retrieve the collection uri and resource name
        $matches = array();

        if(preg_match('/(.*)\/([^\/]*)$/', $uri, $matches)){
            $collection_uri = @$matches[1];
            $resource_name = @$matches[2];
        }else{
            \App::abort(400, 'Provide a collection uri and a resource name in order to create a new definition.');
        }

        // Check if the first collection_uri slug is not part of the occupied uri's
        $collection_parts = explode('/', $collection_uri);
        if(in_array( $collection_parts[0], self::$FORBIDDEN_PREFIX)){
            \App::abort(400, "The collection name, $collection_parts[0], cannot be used as the start of a collection.");
        }

        // Retrieve the content type and parse out the definition type
        $content_type = \Request::header('content_type');

        // Retrieve the parameters of the PUT requests (either a JSON document or a key=value string)
        $params = \Request::getContent();

        // Is the body passed as JSON, if not try getting the request parameters from the uri
        if(!empty($params)){
            $params = json_decode($params, true);
        }else{
            $params = \Input::all();
        }

        // If we get empty params, then something went wrong
        if(empty($params)){
            \App::abort(400, "The parameters could not be parsed from the body or request URI, make sure parameters are provided and if they are correct (e.g. correct JSON).");
        }

        $params = array_change_key_case($params);

        $matches = array();

        // If the source type exists, validate the given properties and if all is well, create the new definition with
        // the provide source type
        if(preg_match('/application\/tdt\.(.*)/', $content_type, $matches)){

            $type = $matches[1];
            $definition_type = ucfirst($type) . "Definition";

            if(class_exists($definition_type)){
                // Validate the given parameters based on the given definition_type
                // The validated parameters should only contain properties that are defined
                // by the source type, meaning no relational parameters
                $validated_params = $definition_type::validate($params);
            }else{
                \App::abort(406, "The requested Content-Type is not supported, look at the discovery document for the supported content-types.");
            }

            $def_instance = new $definition_type();

            // Assign the properties of the new definition_type
            foreach($validated_params as $key => $value){
                $def_instance->$key = $value;
            }

            $def_instance->save($params);

            // Create the definition associated with the new definition instance
            $definition = new \Definition();
            $definition->collection_uri = $collection_uri;
            $definition->resource_name = $resource_name;
            $definition->source_id = $def_instance->id;
            $definition->source_type = ucfirst($type) . 'Definition';

            // Add the create parameters of description to the new description object
            $def_params = array_only($params, array_keys(\Definition::getCreateParameters()));
            foreach($def_params as $property => $value){
                $definition->$property = $value;
            }

            $definition->save();

            $response = \Response::make(null, 200);
            $response->header('Location', \Request::getHost() . '/' . $uri);

            return $response;

        }else{
            \App::abort(400, "The content-type provided was not recognized, look at the discovery document for the supported content-types.");
        }
    }

    /**
     * Validate the create parameters based on the rules of a certain definition.
     * If something goes wrong, abort the application and return a corresponding error message.
     */
    private static function validateParameters($definition, $params){

        $validated_params = array();

        if(class_exists($definition)){

            $create_params = $definition::getCreateParameters();
            $rules = $definition::getCreateValidators();

            foreach($create_params as $key => $info){

                if(!array_key_exists($key, $params)){

                    if(!empty($info['required']) && $info['required']){
                        \App::abort(404, "The parameter $key is required in order to create a defintion but was not provided.");
                    }

                    if(!empty($info['default_value'])){
                        $validated_params[$key] = $info['default_value'];
                    }else{
                        $validated_params[$key] = null;
                    }
                }else{

                    if(!empty($rules[$key])){

                        $validator = \Validator::make(
                            array($key => $params[$key]),
                            array($key => $rules[$key])
                        );

                        if($validator->fails()){
                            \App::abort(404, "The validation failed for parameter $key, make sure the value is valid.");
                        }
                    }

                    $validated_params[$key] = $params[$key];
                }
            }

            return $validated_params;
        }else{
            \App::abort(406, "The content-type provided was not recognized, look at the discovery document for the supported content-types.");
        }
    }

    /**
     * Delete a definition based on the URI given.
     */
    private static function deleteDefinition($uri){

        $definition = self::get($uri);

        if(empty($definition)){
            \App::abort(404, "The given uri, $uri, could not be resolved as a resource that can be deleted.");
        }

        $definition->delete();

        $response = \Response::make(null, 200);
        return $response;
    }

    /**
     * PATCH a definition based on the PATCH parameters and URI.
     */
    private static function updateDefinition($uri){

        // Check if the uri already exists
        if(!self::exists($uri)){
            \App::abort(404, "The given uri ($uri) can't be retrieved as a resource.");
        }

        // Get the definition and his fillable properties
        $definition = self::get($uri);
        $definition_params = $definition->getFillable();

        // Get the source type and his fillable properties
        $source_type = $definition->source()->first();
        $source_params = $source_type->getFillable();

        // Retrieve the parameters of the PUT requests (either a JSON document or a key=value string)
        $params = \Request::getContent();

        // Is the body passed as JSON, if not try getting the request parameters from the uri
        if(!empty($params)){
            $params = json_decode($params, true);
        }else{
            $params = \Input::all();
        }

        if(empty($params)){
            \App::abort(400, 'Failed to parse the parameters, make sure the document is well-formed.');
        }

        // Only keep the properties from the parameters that are appropriate for Definition
        $patched_def_params = array_only($params, $definition_params);

        // Only keep the properties from the parameters that are appropriate for the SourceType
        $patched_source_params = array_only($params, $source_params);

        // Merge the new params with the old ones, and pass them to the source type for validation
        foreach($source_params as $key){
            if(empty($patched_source_params[$key]) && !@is_numeric($patched_source_params[$key])){
                $patched_source_params[$key] = $source_type->$key;
            }
        }

        // Validate the parameters of the SourceType
        $validated_params = $source_type::validate($patched_source_params);

        $source_params = array('source' => $validated_params, 'all' => $params);

        // Pass along the source type validated parameters, and the original parameter set (may contain columns, geo, ...)
        $source_type->update($source_params);
        $definition->update($patched_def_params);

        $response = \Response::make(null, 200);
        $response->header('Location', \Request::getHost() . '/' . $uri);

        return $response;
    }

    /**
     * Return the headers of a call made to the uri given.
     */
    private static function headDefinition($uri){
        \App::abort(500, "Function not yet implemented.");
    }

    /*
     * GET a definition based on the uri provided
     * TODO add support function get retrieve collections, instead full resources.
     */
    private static function viewDefinition($uri){

        // TODO make dynamic
        if(empty($uri)){
            $definitions = \Definition::all();

            $defs_props = array();
            foreach($definitions as $definition){
                $defs_props[$definition->collection_uri . '/' . $definition->resource_name] = $definition->getAllParameters();
            }

            return self::makeResponse(str_replace('\/', '/', json_encode($defs_props)));
        }

        if(!self::exists($uri)){
            \App::abort(404, "No resource has been found with the uri $uri");
        }

        // Get Definition object based on the given uri
        $definition = self::get($uri);

        $def_properties = $definition->getAllParameters();

        return self::makeResponse(str_replace("\/", "/", json_encode($def_properties)));
    }

    /**
     * Get a definition object with the given uri.
     */
    public static function get($uri){
        // Left trim the uri for a /
        $uri = ltrim($uri, '/');

        return \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name , '/', '%')", array($uri . '/'))->first();
    }

    /**
     * Check if a resource exists with a given uri.
     */
    public static function exists($uri){
        $definition = self::get($uri);
        return !empty($definition);
    }

    /**
     * Return the response with the given data ( formatted in json )
     */
    private static function makeResponse($data){

         // Create response
        $response = \Response::make($data, 200);

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

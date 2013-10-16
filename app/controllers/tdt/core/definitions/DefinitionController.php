<?php

namespace tdt\core\definitions;

/**
 * DefinitionController
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DefinitionController extends \Controller {

    public static function handle($uri){

        // Propage the request based on the HTTPMethod of the request.
        $request = \Request::createFromGlobals();

        // Awaiting issue https://github.com/laravel/framework/issues/1655
        //$method = \Request::header('Method');

        $method = $request->getRealMethod();

        switch($method){
            case "PUT":
                self::createDefinition($uri);
                break;
            case "GET":
                // TODO return the existing definitions, these should be seen only by authenticated peopless
                break;
            case "PATCH":
                self::patchDefinition($uri);
                break;
            case "DELETE":
                self::deleteDefinition($uri);
                break;
            default:
                \App::abort(400, "The method $method is not supported by the definitions.");
                break;
        }
    }

    /**
     * Create a new definition based on the PUT parameters given and content-type.
     */
    private static function createDefinition($uri){

        // Retrieve the collection uri and resource name
        $matches = array();

        if(preg_match('/(.*)\/([^\/]*)$/', $uri, $matches)){
            $collection_uri = $matches[1];
            $resource_name = $matches[2];
        }else{
            \App::abort(452, "The uri should at least have a collection uri and a resource name.");
        }

        // Retrieve the parameters of the PUT requests (either a JSON document or a key=value string).
        $request = \Request::createFromGlobals();
        $params = $request->getContent();

        // Is the body passed as JSON, if not try getting the request parameters from the uri.
        if(!empty($params)){
            $params = json_decode($params, true);
        }else{
            $params = $request->query->all();
        }

        // Retrieve the content type and parse out the definition type.
        $content_type = $request->headers->get('content_type');
        $matches = array();

        if(preg_match('/application\/tdt\.(.*)/', $content_type, $matches)){
            $type = $matches[1];
            $definition_type = ucfirst($type) . "Definition";

            if(class_exists($definition_type)){
                // Validate the given parameters based on the given definition_type.
                $validated_params = $definition_type::validate($params);
            }else{
                \App::abort(452, "The content-type provided was not recognized, look at the discovery document for the supported content-types.");
            }

            $def_instance = new $definition_type();

            // Assign the properties of the new definition_type.
            foreach($validated_params as $key => $value){
                $def_instance->$key = $value;
            }

            $def_instance->save();

            // Create the definition associated with the new definition instance.
            $definition = new \Definition();
            $definition->collection_uri = $collection_uri;
            $definition->resource_name = $resource_name;
            $definition->source_id = $def_instance->id;
            $definition->source_type = $type;
            $definition->save();

            $response = \Response::make(null, 200);
            $response->header('Location', $request->getHost() . '/' . $uri);

            return $response;

        }else{
            \App::abort(452, "The content-type provided was not recognized, look at the discovery document for the supported content-types.");
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
                        \App::abort(452, "The parameter $key is required in order to create a defintion but was not provided.");
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
                            \App::abort(452, "The validation failed for parameter $key, make sure the value is valid.");
                        }
                    }

                    $validated_params[$key] = $params[$key];
                }
            }

            return $validated_params;
        }else{
            \App::abort(452, "The content-type provided was not recognized, look at the discovery document for the supported content-types.");
        }
    }

    /**
     * Delete a definition based on the URI given.
     */
    private static function deleteDefinition($uri){

    }

    /**
     * PATCH a definition based on the PATCH parameters and URI.
     */
    private static function patchDefinition($uri){

    }
}
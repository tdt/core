<?php

namespace tdt\core\definitions;

use Illuminate\Routing\Router;
use tdt\core\auth\Auth;
use tdt\core\datasets\Data;
use tdt\core\ContentNegotiator;

/**
 * InfoController
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class InfoController extends \Controller {

    public static function handle($uri){

        // Set permission
        Auth::requirePermissions('info.view');

        // Split for an (optional) extension
        preg_match('/([^\.]*)(?:\.(.*))?$/', $uri, $matches);

        // URI is always the first match
        $uri = $matches[1];

        // Get extension (if set)
        $extension = (!empty($matches[2]))? $matches[2]: null;

        // Propagate the request based on the HTTPMethod of the request
        $method = \Request::getMethod();

        switch($method){
            case "GET":
                return self::getInfo($uri);
                break;
            default:
                // Method not supported
                \App::abort(405, "The HTTP method '$method' is not supported by this resource.");
                break;
        }
    }

    /**
     * Return the headers of a call made to the uri given.
     */
    private static function headDefinition($uri){
        \App::abort(500, "Method not yet implemented.");
    }

    /*
     * GET an info document based on the uri provided
     */
    private static function getInfo($uri){

        if(!empty($uri)){
            if(DefinitionController::exists($uri)){
                $info = self::createInfoObject(DefinitionController::get($uri));
                return self::makeResponse($info);
            }else{
                \App::abort(404, "The given uri ($uri) couldn't be resolved to a resource.");
            }
        }else{
            return self::getDefinitionsInfo();
        }

    }

    /**
     * Return the information about published datasets
     */
    private static function getDefinitionsInfo(){

        // Get all of the definitions
        $definitions = \Definition::all();

        $info = array();

        foreach($definitions as $definition){

            $definition_info = self::createInfoObject($definition);
            $id = $definition->collection_uri . '/' .$definition->resource_name;

            // Add the info to the collection
            $info[$id] = $definition_info;
        }

        // Add DCAT as a resource
        $definition_info = new \stdClass();
        $definition_info->description = "A DCAT document about the available datasets created by using the DCAT vocabulary.";
        $id = 'dcat';
        $definition_info->uri = \Request::root() . '/' . $id;

        // Add the info to the collection
        $info[$id] = $definition_info;

        return self::makeResponse($info);
    }

    /**
     * Create an info object from a definition
     */
    private static function createInfoObject($definition){

        $definition_info = new \stdClass();

        $id = $definition->collection_uri . '/' .$definition->resource_name;
        $definition_info->uri = \Request::root() . '/' . $id;

        // Add the dublin core to the info object
        foreach($definition->getFillable() as $property){
            $definition_info->$property = $definition->$property;
        }

        // Get the available request parameters from the responsible datacontroller
        $source_type = $definition->source()->first();
        $definition_info->description = $source_type->description;

        // Installed source types contain their own set of parameters (required and optional)
        if(strtolower($source_type->getType()) == 'installed'){

            // Include the class
            $class_file = app_path() . '/../installed/' .  $source_type->path;

            if(file_exists($class_file)){

                require_once $class_file;

                $class_name = $source_type->class;

                // Check if class exists
                if(class_exists($class_name)){

                    $installed = new $class_name();
                    $definition_info->parameters = $installed::getParameters();
                }
            }
        }else{

            $datacontroller = '\\tdt\\core\\datacontrollers\\' . $source_type->getType() . 'Controller';
            $params = $datacontroller::getParameters();
            $definition_info->parameters = $params;
        }

        return $definition_info;
    }

    /**
     * Return the response with the given data ( formatted in json )
     */
    private static function makeResponse($data){

         // Create response
        $response = \Response::make(str_replace('\/','/', json_encode($data)));

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

<?php

namespace tdt\core\definitions;

use Illuminate\Routing\Router;
use tdt\core\auth\Auth;
use tdt\core\datasets\Data;
use tdt\core\ContentNegotiator;
use tdt\core\Pager;
use tdt\core\ApiController;

/**
 * InfoController: Controller that handles info requests and returns informational data about the datatank.
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class InfoController extends ApiController {

    public function get($uri){

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
    public function head($uri){

        if(!empty($uri)){
            if(!$this->definition_repository->exists($uri)){
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
    private function getInfo($uri){

        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $definitions_info = $this->definition_repository->getAllDefinitionInfo($uri, $limit, $offset);

        $definition_count = $this->definition_repository->countPublished();

        $result = new Data();
        $result->paging = Pager::calculatePagingHeaders($limit, $offset, $definition_count);
        $result->data = $definitions_info;

        return ContentNegotiator::getResponse($result, 'json');
    }

    /**
     * Return the information about published datasets
     */
    private function getDefinitionsInfo($uri){

        // Apply paging to fetch the definitions
        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $definition_count = $this->definition_repository->countPublished();

        $definitions = $this->definition_repository->getAllPublished($limit, $offset);

        $info = array();

        foreach($definitions as $definition){

            $definition_info = $this->createInfoObject($definition);
            $id = $definition->collection_uri . '/' .$definition->resource_name;

            unset($definition_info->draft);

            // Add the info to the collection
            $info[$id] = $definition_info;
        }

        $result = new Data();
        $result->paging = Pager::calculatePagingHeaders($limit, $offset, $definition_count);
        $result->data = $info;

        return ContentNegotiator::getResponse($result, 'json');
    }

    /**
     * Create an info object from a definition
     */
    private function createInfoObject($definition){

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
        if(strtolower($source_type->type) == 'installed'){

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

            $datacontroller = '\\tdt\\core\\datacontrollers\\' . $source_type->type . 'Controller';
            $params = $datacontroller::getParameters();
            $definition_info->parameters = $params;
        }

        return $definition_info;
    }

    /**
     * Return the response with the given data ( formatted in json )
     */
    private function makeResponse($data){

         // Create response
        $response = \Response::make(str_replace('\/','/', json_encode($data)));

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

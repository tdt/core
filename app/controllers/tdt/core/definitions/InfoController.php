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
                return self::getInfo($uri, $extension);
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

    }

    /*
     * GET an info document based on the uri provided
     * TODO add support function get retrieve collections, instead full resources.
     */
    private static function getInfo($uri, $extension = null){

        // We have different informational resources
        switch($uri){
            case 'dcat':

                // Default format is ttl for dcat
                if(empty($extension)){
                    $extension = 'ttl';
                }

                $dcat = self::createDcat();

                // Allow content nego. for dcat
                return ContentNegotiator::getResponse($dcat, $extension);
                break;
            case 'info':
                // Return the informational properties and uri's of published datasets
                return self::getDefinitionsInfo();
                break;
            default:
                break;
        }
    }

    /**
     * Create the DCAT document of the published resources
     *
     * @param $pieces array of uri pieces
     * @return mixed \Data object with a graph of DCAT information
     */
    private static function createDcat(){

        // List all namespaces that can be used in a DCAT document
        $ns = array(
            'dcat' => 'http://www.w3.org/ns/dcat#',
            'dct'  => 'http://purl.org/dc/terms/',
            'foaf' => 'http://xmlns.com/foaf/0.1/',
            'rdf'  => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
            'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
            'owl'  => 'http://www.w3.org/2002/07/owl#',
        );

        foreach($ns as $prefix => $uri){
            \EasyRdf_Namespace::set($prefix, $uri);
        }

        // Create a new EasyRDF graph
        $graph = new \EasyRdf_Graph();

        $uri = \Request::root();

        // Add the catalog and a title
        $graph->addResource($uri . '/info/dcat', 'a', 'dcat:Catalog');
        $graph->addLiteral($uri . '/info/dcat', 'dct:title', 'A DCAT feed of datasets published by The DataTank.');

        // Add the relationships with the datasets

        $definitions = \Definition::query()->orderBy('updated_at', 'desc')->get();

        if(count($definitions) > 0){
            $last_mod_def = $definitions->first();

            // Add the last modified timestamp in ISO8601
            $graph->addLiteral($uri . '/info/dcat', 'dct:modified', date(\DateTime::ISO8601, strtotime($last_mod_def->updated_at)));
            $graph->addLiteral($uri . '/info/dcat', 'foaf:homepage', $uri);

            foreach($definitions as $definition){

                // Create the dataset uri
                $dataset_uri = $uri . "/" . $definition->collection_uri . "/" . $definition->resource_name;

                $source_type = $definition->source()->first();

                // Add the dataset link to the catalog
                $graph->addResource($uri . '/info/dcat', 'dcat:Dataset', $dataset_uri);

                // Add the dataset resource and its description
                $graph->addResource($dataset_uri, 'a', 'dcat:Dataset');
                $graph->addLiteral($dataset_uri, 'dct:description', @$source_type->description);
                $graph->addLiteral($dataset_uri, 'dct:issued', date(\DateTime::ISO8601, strtotime($definition->created_at)));
                $graph->addLiteral($dataset_uri, 'dct:modified', date(\DateTime::ISO8601, strtotime($definition->updated_at)));
            }
        }


        // Get the triples from our created graph
        $triples = $graph->serialise('turtle');

        // Parse them into an ARC2 graph (this is our default graph wrapper in our core functionality)
        $parser = \ARC2::getTurtleParser();
        $parser->parse('', $triples);

        // Return the dcat feed in our internal data object
        $data_result = new Data();
        $data_result->data = $parser;
        $data_result->is_semantic = true;

        // Add the semantic configuration for the ARC graph
        $data_result->semantic = new \stdClass();
        $data_result->semantic->conf = array('ns' => $ns);
        $data_result->definition = new \stdClass();
        $data_result->definition->resource_name = 'dcat';
        $data_result->definition->collection_uri = 'info';

        return $data_result;
    }

    /**
     * Return the information about published datasets
     */
    private static function getDefinitionsInfo(){

        // Get all of the definitions
        $definitions = \Definition::all();

        $info = array();

        foreach($definitions as $definition){

            $definition_info = new \stdClass();

            $id = $definition->collection_uri . '/' .$definition->resource_name;
            $definition_info->uri = \Request::root() . '/' . $id;

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

            // Add the info to the collection
            $info[$id] = $definition_info;
        }

        // Add DCAT as a resource
        $definition_info = new \stdClass();
        $definition_info->description = "A DCAT document about the available datasets created by using the DCAT vocabulary.";
        $id = 'info/dcat';
        $definition_info->uri = \Request::root() . '/' . $id;

        // Add the info to the collection
        $info[$id] = $definition_info;

        return self::makeResponse($info);
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

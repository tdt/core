<?php

namespace tdt\core\definitions;

use Illuminate\Routing\Router;
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

        // Propagate the request based on the HTTPMethod of the request
        $method = \Request::getMethod();

        // Split for an (optional) extension
        preg_match('/([^\.]*)(?:\.(.*))?$/', $uri, $matches);

        // URI is always the first match
        $uri = $matches[1];

        // Get extension (if set)
        $extension = (!empty($matches[2]))? $matches[2]: null;

        switch($method){
            case "GET":
                $data = self::getInfo($uri);
                break;
            default:
                \App::abort(400, "The method $method is not supported by the info resource.");
                break;
        }

        // We expect a format and a data object to be returned
        return ContentNegotiator::getResponse($data, $extension);
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
    private static function getInfo($uri){

        // Split the uri in its pieces
        $pieces = explode('/', $uri);

        // Get the first piece
        $resource = array_shift($pieces);

        // We have different informational resources
        switch($resource){
            case 'dcat':
                return self::createDcat($pieces);
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
    private static function createDcat($pieces){

        // List all namespaces that can be used in a DCAT document
        $ns = array("dcat" => "http://www.w3.org/ns/dcat#",
                    "dct"  => "http://purl.org/dc/terms/",
                    "rdf"  => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
                    "rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
                    "owl"  => "http://www.w3.org/2002/07/owl#",
        );

        // Create a new EasyRDF graph
        $graph = new \EasyRdf_Graph();

        $uri = \Request::root();

        // Add the catalog and a title
        $graph->addResource('http://www.w3.org/ns/dcat#Catalog', 'a', 'dcat:catalog');
        $graph->addLiteral('http://www.w3.org/ns/dcat#Catalog', 'dct:title', 'A DCAT feed of datasets on the datatank hosted on ' . $uri);

        // Add the relationships with the datasets
        $definitions = \Definition::all();

        foreach($definitions as $definition){

            // Create the dataset uri
            $dataset_uri = $uri . "/" . $definition->collection_uri . "/" . $definition->resource_name;

            // Add the dataset link to the catalog
            $graph->addResource('http://www.w3.org/ns/dcat#Catalog', 'dcat:dataset', $dataset_uri);

            // Add the dataset resource and its description
            $graph->addResource($dataset_uri, 'a', 'dcat:dataset');
            $graph->addLiteral($dataset_uri, 'dct:description', $definition->description);
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

        return $data_result;
    }
}

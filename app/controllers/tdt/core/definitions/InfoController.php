<?php

namespace tdt\core\definitions;

use Illuminate\Routing\Router;
use tdt\core\datasets\Data;



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

        switch($method){
            case "GET":
                return self::getInfo($uri);
                break;
            default:
                \App::abort(400, "The method $method is not supported by the info resource.");
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
    private static function getInfo($uri){

        // Split the uri in its pieces
        $pieces = explode('/', $uri);

        // Get the first piece
        $resource = array_shift($pieces);

        // We have different informational resources
        switch($resource){

            case 'dcat':
                return self::createDcat();
                break;
            default:
                break;

        }
    }

    /**
     * Create the DCAT document of the published resources.
     * TODO perhaps an easier way can be found to create and alter a graph from scratch
     */
    private static function createDcat(){

        // List all namespaces that can be used in a DCAT document
        $ns = array("dcat" => "http://www.w3.org/ns/dcat#",
                    "dct"  => "http://purl.org/dc/terms/",
                    "rdf"  => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
                    "rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
                    "owl"  => "http://www.w3.org/2002/07/owl#",
        );

        // Retrieve all the identifiers of the resources
        $definitions = \Definition::all();

        $identifiers = '';
        foreach($definitions as $definition){
            $identifiers = $definition->collection_uri . '/' . $definition->resource_name;
        }

        $dcat_document = '';

        foreach($ns as $prefix => $uri){
            $dcat_document .= "@prefix $prefix: <$prefix>";
        }

        // Retrieve the information we need to create dataset nodes from our resources


        // Parse the DCAT document and return an ARC graph
        $parser = \ARC2::getTurtleParser();
        $parser->parse('', $dcat_document);

        return $parser;
    }
}

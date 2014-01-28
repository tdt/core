<?php

namespace tdt\core\definitions;

use Illuminate\Routing\Router;
use tdt\core\auth\Auth;
use tdt\core\datasets\Data;
use tdt\core\ContentNegotiator;

/**
 * DcatController
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DcatController extends \Controller {

    public static function handle($uri){

        // Set permission
        Auth::requirePermissions('info.view');

        // Split for an (optional) extension, dcat might use $uri in the future
        preg_match('/([^\.]*)(?:\.(.*))?$/', $uri, $matches);

        // URI is always the first match
        $uri = $matches[1];

        // Get extension (if set)
        $extension = (!empty($matches[2]))? $matches[2]: null;

        // Propagate the request based on the HTTPMethod of the request
        $method = \Request::getMethod();

        switch($method){
            case "GET":
                $dcat = self::createDcat();

                // Default format is ttl for dcat
                if(empty($extension)){
                    $extension = 'ttl';
                }

                $dcat = self::createDcat();

                // Allow content nego. for dcat
                return ContentNegotiator::getResponse($dcat, $extension);
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
                $dataset_uri = str_replace(' ', '%20', $dataset_uri);

                $source_type = $definition->source()->first();

                // Add the dataset link to the catalog
                $graph->addResource($uri . '/info/dcat', 'dcat:dataset', $dataset_uri);

                // Add the dataset resource and its description
                $graph->addResource($dataset_uri, 'a', 'dcat:Dataset');
                $graph->addLiteral($dataset_uri, 'dct:description', @$source_type->description);
                $graph->addLiteral($dataset_uri, 'dct:identifier', str_replace(' ', '%20', $definition->collection_uri . '/' . $definition->resource_name));
                $graph->addLiteral($dataset_uri, 'dct:issued', date(\DateTime::ISO8601, strtotime($definition->created_at)));
                $graph->addLiteral($dataset_uri, 'dct:modified', date(\DateTime::ISO8601, strtotime($definition->updated_at)));

                // Add the source resource if it's a URI
                if (strpos($definition->source, 'http://') !== false || strpos($definition->source, 'https://')){
                    $graph->addResource($dataset_uri, 'dct:source', str_replace(' ', '%20', $definition->source));
                }

                // Optional dct terms
                $optional = array('title', 'date', 'language', 'rights');

                foreach($optional as $dc_term){
                    if(!empty($definition->$dc_term)){

                        // TODO decide dynamically based on the declaration of DCAT properties
                        if($dc_term == 'rights'){
                            $license = @\License::where('title', '=', $definition->$dc_term)->first()->toArray();

                            if(!empty($license) && !empty($license['url'])){
                                $graph->addResource($dataset_uri, 'dct:' . $dc_term, $license['url']);
                            }
                        }elseif($dc_term == 'language'){
                            $lang = @\Language::where('name', '=', $definition->$dc_term)->first()->toArray();
                            if(!empty($lang)){
                                $graph->addResource($dataset_uri, 'dct:' . $dc_term, 'http://lexvo.org/id/iso639-3/' . $lang['lang_id']);
                            }
                        }else{
                            $graph->addLiteral($dataset_uri, 'dct:' . $dc_term, $definition->$dc_term);
                        }
                    }
                }
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
}

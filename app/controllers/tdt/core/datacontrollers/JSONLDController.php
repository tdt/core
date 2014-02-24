<?php

namespace tdt\core\datacontrollers;

use tdt\core\cache\Cache;
use tdt\core\datasets\Data;

use Symfony\Component\HttpFoundation\Request;

use ML\JsonLD\JsonLD;
use ML\JsonLD\NQuads;
use ML\JsonLD\Exception\JsonLdException;
use ML\JsonLD\Exception\ParseException;
use ML\JsonLD\Exception\InvalidQuadException;

/**
 * JSON-LD Controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class JSONLDController extends ADataController {

    public function readData($source_definition, $rest_parameters = array()){

        $uri = $source_definition->uri;

        // If the parsing in the document fails, a JsonLdException is thrown
        try{

            $rdf = JsonLD::toRdf($uri);

        }catch(JsonLdException $ex){
            \App::abort(500, "The JSON LD reader couldn't parse the document, the exception message we got is: " . $ex->getMessage());
        }catch(Exception $ex){
            \App::abort(500, "The JSON LD reader couldn't parse the document, the exception message we got is: " . $ex->getMessage());
        }

        // Convert the JSON-LD to nquad triples
        try{
            $nquads = new NQuads();
            $rdf = $nquads->serialize($rdf);
        }catch(ParseException $ex){
            \App::abort(500, "The JSON LD reader couldn't parse the document, the exception message we got is: " . $ex->getMessage());
        }catch(InvalidQuadException $ex){
            \App::abort(500, "The JSON LD reader couldn't parse the document, the exception message we got is: " . $ex->getMessage());
        }

        // Convert the nquad triples to an EasyRdf graph for further processing
        $graph = new \EasyRdf_Graph();
        $parser = new \EasyRdf_Parser_Ntriples();

        $parser->parse($graph, $rdf, 'ntriples', null);

        // Return the data object with the graph
        $data = new Data();
        $data->data = $graph;
        $data->is_semantic = true;

        return $data;
    }
}

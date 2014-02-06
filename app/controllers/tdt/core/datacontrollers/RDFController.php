<?php

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;
use Symfony\Component\HttpFoundation\Request;

define("RDFAPI_INCLUDE_DIR", app_path() . '/lib/rdfapi-php/api/');

include(RDFAPI_INCLUDE_DIR . "RDFAPI.php");

/**
 * Turtle Controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class RDFController extends ADataController {

    public function readData($source_definition, $rest_parameters = array()){

        // Fetch the URI of the turtle file
        $uri = $source_definition->uri;
        $format = strtolower($source_definition->format);

        $content = file_get_contents($uri);

        // Try parsing the contents of the rdf file
        $graph = new \EasyRdf_Graph();
        $parser;

        // We only support turtle or xml
        if($format == 'turtle'){
            $parser = new \EasyRdf_Parser_Turtle();
        }elseif($format == 'xml'){
            $parser = new \EasyRdf_Parser_RdfXml();
        }else{
            \App::abort(500, "The format you added, $format, is not supported. The supported formats are turtle and xml.");
        }

        // Check if triples are added to the graph from the contents
        $triples_added = $parser->parse($graph, $content, $format, '');

        // If no triples were parsed, abort
        if($triples_added == 0){
            \App::abort(500, 'No triples were added using the ' . $format . ' parser.');
        }

        // Serialize the graph in json
        $rdf = $graph->serialise('json');

        // Fetch the query from the uri
        $query = \Request::query('query');

        $factory = new \ModelFactory();

        // Create an in memory model of the rdf string, which is in json
        $mem_model = $factory->getMemModel();
        $mem_model->loadFromString($rdf, 'json');

        // We cannot go further here, as to say that we don't return
        // RDF/XML or JSON, unless we implement that ourselves, just return the sparql query results
        $result = $mem_model->sparqlQuery($query);

        $data = new Data();
        $data->data = $result;
        $data->is_semantic = false;

        return $data;
    }
}

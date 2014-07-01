<?php

namespace Tdt\Core\Datacontrollers;

use Tdt\Core\Cache\Cache;
use Tdt\Core\Datasets\Data;

use Symfony\Component\HttpFoundation\Request;

/**
 * JSON-LD Controller
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class JSONLDController extends ADataController
{

    public function readData($source_definition, $rest_parameters = array())
    {
        $uri = $source_definition['uri'];

        // If the parsing in the document fails, a JsonLdException is thrown
        try {

            $graph = new \EasyRdf_Graph();

            if ((substr($uri, 0, 4) == "http")) {
                $graph = \EasyRdf_Graph::newAndLoad($uri);
            } else {
                $graph->parseFile($uri, 'jsonld');
            }

        } catch (\Exception $ex) {
            \App::abort(500, "The JSON LD reader couldn't parse the document, the exception message we got is: " . $ex->getMessage());
        }

        // Return the data object with the graph
        $data = new Data();
        $data->data = $graph;
        $data->is_semantic = true;
        $data->preferred_formats = $this->getPreferredFormats();

        return $data;
    }

    protected function getPreferredFormats()
    {
        return array('jsonld', 'ttl', 'rdf');
    }
}

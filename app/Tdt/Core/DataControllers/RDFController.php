<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Datasets\Data;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller that reads RDF from XML or Turtle files.
 *
 * @copyright (C) 2011,2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class RDFController extends ADataController
{

    public function readData($source_definition, $rest_parameters = array())
    {
        // Fetch the URI of the rdf file
        $uri = $source_definition['uri'];

        $format = strtolower($source_definition['format']);

        $content = file_get_contents($uri);

        // Try parsing the contents of the rdf file
        $graph = new \EasyRdf_Graph();

        $parser;

        if ($format == 'turtle') {
            $parser = new \EasyRdf_Parser_Turtle();
        } elseif ($format == 'xml') {
            // EasyRdf identifies rdfxml with rdf, not with xml as a format
            $format = 'rdfxml';
            $parser = new \EasyRdf_Parser_RdfXml();
        } else {
            \App::abort(500, "The format you added, $format, is not supported. The supported formats are turtle and xml.");
        }

        $triples_added = $parser->parse($graph, $content, $format, '');

        $data = new Data();
        $data->data = $graph;
        $data->is_semantic = true;
        $data->preferred_formats = $this->getPreferredFormats();

        return $data;
    }

    protected function getPreferredFormats()
    {
        array('rdf', 'ttl', 'jsonld');
    }
}

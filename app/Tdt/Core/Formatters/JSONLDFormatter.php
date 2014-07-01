<?php

namespace Tdt\Core\Formatters;

use ML\JsonLD\JsonLD;

/**
 * JSON-LD Formatter
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class JSONLDFormatter implements IFormatter
{

    public static function createResponse($dataObj)
    {
        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'application/ld+json;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj)
    {

        if ($dataObj->is_semantic) {

            // Check if a configuration is given
            $conf = array();

            if (!empty($dataObj->semantic->conf)) {

                $conf = $dataObj->semantic->conf;

                foreach ($conf as $prefix => $uri) {
                    \EasyRdf_Namespace::set($prefix, $uri);
                }
            }

            // Add the configured ontology prefixes
            $ontologies =\App::make('Tdt\Core\Repositories\Interfaces\OntologyRepositoryInterface');

            $context = array();

            // Only add the common namespaces
            $namespaces = array('hydra', 'rdf', 'rdfs', 'foaf', 'void', 'xsd', 'skos', 'xs');

            foreach ($namespaces as $ns) {

                $namespace = $ontologies->getByPrefix($ns);

                if (!empty($namespace)) {
                    $context[$ns] = $namespace['uri'];
                }
            }

            $output = $dataObj->data->serialise('jsonld');

            // Next, encode the context as JSON
            $jsonContext = json_encode($context);

            // Compact the JsonLD by using @context -> Needs tweaking can only return the
            // URI spaces that are used in the document.
            $compacted = JsonLD::compact($output, $jsonContext);

            // Print the resulting JSON-LD!
            return JsonLD::toString($compacted, true);

        } else {
            \App::abort(400, "The data is not a semantically linked document, a linked data JSON representation is not possible.");
        }

    }

    public static function getDocumentation()
    {
        return "A JsonLD formatter.";
    }
}

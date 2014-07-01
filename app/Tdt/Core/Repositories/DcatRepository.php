<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\DcatRepositoryInterface;

class DcatRepository implements DcatRepositoryInterface
{
    /**
     * Return a DCAT document based on the definitions that are passed
     *
     * @param array Array with definition configurations
     *
     * @return \EasyRdf_Graph
     */
    public function getDcatDocument(array $definitions, $oldest_definition)
    {
        $graph = new \EasyRdf_Graph();

        $this->licenses = \App::make('Tdt\Core\Repositories\Interfaces\LicenseRepositoryInterface');
        $this->languages = \App::make('Tdt\Core\Repositories\Interfaces\LanguageRepositoryInterface');

        $uri = \Request::root();

        // Add the catalog and a title
        $graph->addResource($uri . '/info/dcat', 'a', 'dcat:Catalog');
        $graph->addLiteral($uri . '/info/dcat', 'dct:title', 'A DCAT feed of datasets published by The DataTank.');

        // Create a new EasyRDF graph
        $graph = new \EasyRdf_Graph();

        if (count($definitions) > 0) {

            // Add the last modified timestamp in ISO8601
            $graph->addLiteral($uri . '/info/dcat', 'dct:modified', date(\DateTime::ISO8601, strtotime($oldest_definition['updated_at'])));
            $graph->addLiteral($uri . '/info/dcat', 'foaf:homepage', $uri);

            foreach ($definitions as $definition) {

                // Create the dataset uri
                $dataset_uri = $uri . "/" . $definition['collection_uri'] . "/" . $definition['resource_name'];
                $dataset_uri = str_replace(' ', '%20', $dataset_uri);

                $source_type = $definition['type'];

                // Add the dataset link to the catalog
                $graph->addResource($uri . '/info/dcat', 'dcat:dataset', $dataset_uri);

                // Add the dataset resource and its description
                $graph->addResource($dataset_uri, 'a', 'dcat:Dataset');
                $graph->addLiteral($dataset_uri, 'dct:description', @$source_type->description);
                $graph->addLiteral($dataset_uri, 'dct:identifier', str_replace(' ', '%20', $definition['collection_uri'] . '/' . $definition['resource_name']));
                $graph->addLiteral($dataset_uri, 'dct:issued', date(\DateTime::ISO8601, strtotime($definition['created_at'])));
                $graph->addLiteral($dataset_uri, 'dct:modified', date(\DateTime::ISO8601, strtotime($definition['updated_at'])));

                // Add the source resource if it's a URI
                if (strpos($definition['source'], 'http://') !== false || strpos($definition['source'], 'https://')) {
                    $graph->addResource($dataset_uri, 'dct:source', str_replace(' ', '%20', $definition['source']));
                }

                // Optional dct terms
                $optional = array('title', 'date', 'language', 'rights');

                foreach ($optional as $dc_term) {

                    if (!empty($definition[$dc_term])) {

                        if ($dc_term == 'rights') {

                            $license = $this->licenses->getByTitle($definition[$dc_term]);

                            if (!empty($license) && !empty($license['url'])) {
                                $graph->addResource($dataset_uri, 'dct:' . $dc_term, $license['url']);
                            }
                        } elseif ($dc_term == 'language') {

                            $lang = $this->languages->getById($definition[$dc_term]);

                            if (!empty($lang)) {
                                $graph->addResource($dataset_uri, 'dct:' . $dc_term, 'http://lexvo.org/id/iso639-3/' . $lang['lang_id']);
                            }
                        } else {
                            $graph->addLiteral($dataset_uri, 'dct:' . $dc_term, $definition[$dc_term]);
                        }
                    }
                }
            }
        }

        return $graph;
    }

    /**
     * Return the used namespaces in the DCAT document
     *
     * @return array
     */
    public function getNamespaces()
    {
        return array(
            'dcat' => 'http://www.w3.org/ns/dcat#',
            'dct'  => 'http://purl.org/dc/terms/',
            'foaf' => 'http://xmlns.com/foaf/0.1/',
            'rdf'  => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
            'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
            'owl'  => 'http://www.w3.org/2002/07/owl#',
        );
    }
}

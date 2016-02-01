<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\DcatRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\LicenseRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\LanguageRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\SettingsRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\ThemeRepositoryInterface;
use User;

class DcatRepository implements DcatRepositoryInterface
{
    private $GEO_TYPES = ['ShpDefinition'];

    public function __construct(
        LicenseRepositoryInterface $licenses,
        LanguageRepositoryInterface $languages,
        SettingsRepositoryInterface $settings,
        ThemeRepositoryInterface $themes
    ) {
        $this->licenses = $licenses;
        $this->languages = $languages;
        $this->settings = $settings;
        $this->themes = $themes;
    }

    /**
     * Return a DCAT document based on the definitions that are passed
     *
     * @param array Array with definition configurations
     *
     * @return \EasyRdf_Graph
     */
    public function getDcatDocument(array $definitions, $oldest_definition)
    {
        // Create a new EasyRDF graph
        $graph = new \EasyRdf_Graph();

        $all_settings = $this->settings->getAll();

        $uri = \Request::root();

        // Add the catalog and a title
        $graph->addResource($uri . '/api/dcat', 'a', 'dcat:Catalog');

        $graph->addLiteral($uri . '/api/dcat', 'dct:title', $all_settings['catalog_title']);

        // Fetch the catalog description, issued date and language
        $graph->addLiteral($uri . '/api/dcat', 'dct:description', $all_settings['catalog_description']);
        $graph->addLiteral($uri . '/api/dcat', 'dct:issued', $this->getIssuedDate());

        $lang = $this->languages->getByCode($all_settings['catalog_language']);

        if (!empty($lang)) {
            $graph->addResource($uri . '/api/dcat', 'dct:language', 'http://lexvo.org/id/iso639-3/' . $lang['lang_id']);
            $graph->addResource('http://lexvo.org/id/iso639-3/' . $lang['lang_id'], 'a', 'dct:LinguisticSystem');
        }

        // Fetch the homepage and rights
        $graph->addResource($uri . '/api/dcat', 'foaf:homepage', $uri);
        $graph->addResource($uri . '/api/dcat', 'dct:license', 'http://www.opendefinition.org/licenses/cc-zero');
        $graph->addResource('http://www.opendefinition.org/licenses/cc-zero', 'a', 'dct:LicenseDocument');

        // Add the publisher resource to the catalog
        $graph->addResource($uri . '/api/dcat', 'dct:publisher', $all_settings['catalog_publisher_uri']);
        $graph->addResource($all_settings['catalog_publisher_uri'], 'a', 'foaf:Agent');
        $graph->addLiteral($all_settings['catalog_publisher_uri'], 'foaf:name', $all_settings['catalog_publisher_name']);

        if (count($definitions) > 0) {
            // Add the last modified timestamp in ISO8601
            $graph->addLiteral($uri . '/api/dcat', 'dct:modified', date(\DateTime::ISO8601, strtotime($oldest_definition['updated_at'])));

            foreach ($definitions as $definition) {
                // Create the dataset uri
                $dataset_uri = $uri . "/" . $definition['collection_uri'] . "/" . $definition['resource_name'];
                $dataset_uri = str_replace(' ', '%20', $dataset_uri);

                $source_type = $definition['type'];

                // Add the dataset link to the catalog
                $graph->addResource($uri . '/api/dcat', 'dcat:dataset', $dataset_uri);

                // Add the dataset resource and its description
                $graph->addResource($dataset_uri, 'a', 'dcat:Dataset');

                $title = null;
                if (!empty($definition['title'])) {
                    $title = $definition['title'];
                } else {
                    $title = $definition['collection_uri'] . '/' . $definition['resource_name'];
                }

                $graph->addLiteral($dataset_uri, 'dct:title', $title);


                // Add the description, identifier, issued date, modified date, contact point and landing page of the dataset
                $graph->addLiteral($dataset_uri, 'dct:description', @$definition['description']);
                $graph->addLiteral($dataset_uri, 'dct:identifier', str_replace(' ', '%20', $definition['collection_uri'] . '/' . $definition['resource_name']));
                $graph->addLiteral($dataset_uri, 'dct:issued', date(\DateTime::ISO8601, strtotime($definition['created_at'])));
                $graph->addLiteral($dataset_uri, 'dct:modified', date(\DateTime::ISO8601, strtotime($definition['updated_at'])));
                $graph->addResource($dataset_uri, 'dcat:landingPage', $dataset_uri);

                // Backwards compatibility
                if (!empty($definition['contact_point'])) {
                    $graph->addResource($dataset_uri, 'dcat:contactPoint', $definition['contact_point']);
                }

                // Add the publisher resource to the dataset
                if (!empty($definition['publisher_name']) && !empty($definition['publisher_uri'])) {
                    $graph->addResource($dataset_uri, 'dct:publisher', $definition['publisher_uri']);
                    $graph->addResource($definition['publisher_uri'], 'a', 'foaf:Agent');
                    $graph->addLiteral($definition['publisher_uri'], 'foaf:name', $definition['publisher_name']);
                }

                // Add the keywords to the dataset
                if (!empty($definition['keywords'])) {
                    foreach (explode(',', $definition['keywords']) as $keyword) {
                        $keyword = trim($keyword);
                        $graph->addLiteral($dataset_uri, 'dcat:keyword', $keyword);
                    }
                }

                // Add the source resource if it's a URI
                if (strpos($definition['source'], 'http://') !== false || strpos($definition['source'], 'https://')) {
                    $graph->addResource($dataset_uri, 'dct:source', str_replace(' ', '%20', $definition['source']));
                }

                // Optional dct terms
                $optional = array('date', 'language', 'theme');

                foreach ($optional as $dc_term) {
                    if (!empty($definition[$dc_term])) {
                        if ($dc_term == 'language') {
                            $lang = $this->languages->getByName($definition[$dc_term]);

                            if (!empty($lang)) {
                                $graph->addResource($dataset_uri, 'dct:' . $dc_term, 'http://lexvo.org/id/iso639-3/' . $lang['lang_id']);
                                $graph->addResource('http://lexvo.org/id/iso639-3/' . $lang['lang_id'], 'a', 'dct:LinguisticSystem');
                            }
                        } elseif ($dc_term == 'theme') {
                            $theme = $this->themes->getByLabel($definition[$dc_term]);

                            if (!empty($theme)) {
                                $graph->addResource($dataset_uri, 'dcat:' . $dc_term, $theme['uri']);
                                $graph->addLiteral($theme['uri'], 'rdfs:label', $theme['label']);
                            }

                        } else {
                            $graph->addLiteral($dataset_uri, 'dct:' . $dc_term, $definition[$dc_term]);
                        }
                    }
                }

                // Add the distribution of the dataset
                if ($this->isDataGeoFormatted($definition)) {
                    $distribution_uri = $dataset_uri . '.geojson';
                } else {
                    $distribution_uri = $dataset_uri . '.json';
                }

                $graph->addResource($dataset_uri, 'dcat:distribution', $distribution_uri);
                $graph->addResource($distribution_uri, 'a', 'dcat:Distribution');
                $graph->addResource($distribution_uri, 'dcat:accessURL', $dataset_uri);
                $graph->addResource($distribution_uri, 'dcat:downloadURL', $distribution_uri);
                $graph->addLiteral($distribution_uri, 'dct:title', $title);
                $graph->addLiteral($distribution_uri, 'dct:description', 'A json feed of ' . $dataset_uri);
                $graph->addLiteral($distribution_uri, 'dcat:mediaType', 'application/json');
                $graph->addLiteral($distribution_uri, 'dct:issued', date(\DateTime::ISO8601, strtotime($definition['created_at'])));

                // Add the license to the distribution
                if (!empty($definition['rights'])) {
                    $license = $this->licenses->getByTitle($definition['rights']);

                    if (!empty($license) && !empty($license['url'])) {
                        $graph->addResource($dataset_uri . '.json', 'dct:license', $license['url']);
                        $graph->addResource($license['url'], 'a', 'dct:LicenseDocument');
                    }
                }
            }
        }

        return $graph;
    }

    private function isDataGeoFormatted($definition)
    {
        return (
            $definition['type'] == 'shp' ||
            ($definition['type'] == 'json' && !empty($definition['geo_formatted']) && $definition['geo_formatted']) ||
            ($definition['type'] == 'xml' && !empty($definition['geo_formatted']) && $definition['geo_formatted'])
            );
    }

    /**
     * Return the issued date (in ISO8601 standard) of the catalog
     *
     * @return string
     */
    private function getIssuedDate()
    {
        // Fetch the youngest user, and retrieve the timestamp
        // as this user has been created during the installation of the datatank
        $created_at = \DB::table('users')->min('created_at');

        return date(\DateTime::ISO8601, strtotime($created_at));
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

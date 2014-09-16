<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\DcatRepositoryInterface;
use User;

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
        // Create a new EasyRDF graph
        $graph = new \EasyRdf_Graph();

        $this->licenses = \App::make('Tdt\Core\Repositories\Interfaces\LicenseRepositoryInterface');
        $this->languages = \App::make('Tdt\Core\Repositories\Interfaces\LanguageRepositoryInterface');
        $this->settings = \App::make('Tdt\Core\Repositories\Interfaces\SettingsRepositoryInterface');

        $uri = \Request::root();

        // Add the catalog and a title
        $graph->addResource($uri . '/api/dcat', 'a', 'dcat:Catalog');

        $catalog_title = $this->getCatalogTitle();

        $graph->addLiteral($uri . '/api/dcat', 'dct:title', $catalog_title);

        // Fetch the catalog description or fill in the default
        $catalog_description = $this->getCatalogDescription();

        $graph->addLiteral($uri . '/api/dcat', 'dct:description', $catalog_description);
        $graph->addLiteral($uri . '/api/dcat', 'dct:issued', $this->getIssuedDate());
        $graph->addLiteral($uri . '/api/dcat', 'dct:language', $this->getCatalogLanguage());

        $graph->addResource($uri . '/api/dcat', 'foaf:homepage', $uri);
        $graph->addResource($uri . '/api/dcat', 'dct:rights', 'http://www.opendefinition.org/licenses/cc-zero');

        // Add the publisher of the data catalog
        $graph->addResource($uri . '/api/dcat', 'dct:publisher', $this->getCatalogPublisherUri());
        $graph->addResource('http://thedatatank.com', 'a', 'foaf:Agent');
        $graph->addLiteral('http://thedatatank.com', 'foaf:name', $this->getCatalogPublisherName());

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
                $graph->addLiteral($dataset_uri, 'dct:description', @$definition['description']);
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
     * Return the title of the catalog
     *
     * @return string
     */
    private function getCatalogTitle()
    {
        // Fetch the title, or fill in the default title
        $catalog_title = 'The DataTank Datasets';

        $title_setting = $this->settings->getValue('catalog_title');

        if (!empty($title_setting)) {
            $catalog_title = $title_setting;
        }

        return $catalog_title;
    }

    /**
     * Return the description of the catalog
     *
     * @return string
     */
    private function getCatalogDescription()
    {
        $catalog_description = 'A list of datasets published by The DataTank.';

        $description_setting = $this->settings->getValue('catalog_description');

        if (!empty($description_setting)) {
            $catalog_description = $description_setting;
        }

        return $catalog_description;
    }

    /**
     * Return the language of the catalog
     *
     * @return string
     */
    private function getCatalogLanguage()
    {
        $catalog_language = 'en';

        $language_setting = $this->settings->getValue('catalog_language');

        if (!empty($language_setting)) {
            $catalog_language = $language_setting;
        }

        return $catalog_language;
    }

    /**
     * Return the publisher URI of the catalog
     *
     * @return string
     */
    private function getCatalogPublisherUri()
    {
        $catalog_publisher_uri = 'http://thedatatank.com';

        $publisher_uri_setting = $this->settings->getValue('catalog_publisher_uri');

        if (!empty($publisher_uri_setting)) {
            $catalog_publisher_uri = $publisher_uri_setting;
        }

        return $catalog_publisher_uri;
    }

    /**
     * Return the publisher name of the catalog
     *
     * @return string
     */
    private function getCatalogPublisherName()
    {
        $catalog_publisher_name = 'The DataTank';

        $publisher_name_setting = $this->settings->getValue('catalog_publisher_name');

        if (!empty($publisher_name_setting)) {
            $catalog_publisher_name = $publisher_name_setting;
        }

        return $catalog_publisher_name;
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

<?php

namespace Tdt\Core\Formatters;

use EasyRdf\Graph;

/**
 * HTML Formatter
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class HTMLFormatter implements IFormatter
{
    private $GEO_TYPES = ['ShpDefinition'];

    public static function createResponse($dataObj)
    {
        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'text/html; charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj)
    {
        // Query parameters
        $query_string = '';

        if (!empty($_GET)) {
            $get_params = \Input::get();

            foreach ($get_params as $param => $val) {
                if (!empty($val)) {
                    $query_string .= "&$param=$val";
                }
            }

            if (!empty($query_string)) {
                $query_string = trim($query_string, '&');
                $query_string = '?' . $query_string;
            }
        }

        // Links to pages
        $prev_link = '';
        $next_link = '';

        if (!empty($dataObj->paging)) {
            $input_array = array_except(\Input::get(), array('limit', 'offset'));

            $query_string = '';
            if (!empty($input_array)) {
                foreach ($get_params as $param => $val) {
                    if (!empty($val)) {
                        $query_string .= "&$param=$val";
                    }
                }

                if (!empty($query_string)) {
                    $query_string = trim($query_string, '&');
                    $query_string = '?' . $query_string;
                }
            }

            if (!empty($dataObj->paging['previous'])) {
                $prev_link = '?offset=' . $dataObj->paging['previous'][0] . '&limit=' . $dataObj->paging['previous'][1] . $query_string;
            }

            if (!empty($dataObj->paging['next'])) {
                $next_link = '?offset=' . $dataObj->paging['next'][0] . '&limit=' . $dataObj->paging['next'][1] . $query_string;
            }
        }

        // Create the link to the dataset
        $dataset_link  = \URL::to($dataObj->definition['collection_uri'] . "/" . $dataObj->definition['resource_name']);

        // Append rest parameters
        if (!empty($dataObj->rest_parameters)) {
            $dataset_link .= '/' . implode('/', $dataObj->rest_parameters);
        }

        if (!empty($dataObj->source_definition)) {
            $type = $dataObj->source_definition['type'];

                // Check if other views need to be served
            switch ($type) {
                case 'XLS':
                case 'CSV':
                    $first_row = array_shift($dataObj->data);
                    array_unshift($dataObj->data, $first_row);

                    if (is_array($first_row) || is_object($first_row)) {
                        $view = 'dataset.tabular';
                        $data = $dataObj->data;

                    } else {
                        $view = 'dataset.code';
                        $data = self::displayTree($dataObj->data);

                    }

                    break;
                case 'KML':
                case 'SHP':
                    $view = 'dataset.map';
                    $data = $dataset_link . '.map' . $query_string;

                    break;
                case 'XML':
                    $view = 'dataset.code';
                    $data = self::displayTree($dataObj->data, 'xml');
                    break;
                case 'REMOTE':
                    $view = 'dataset.remote';
                    $definitions = \App::make('Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface');
                    $properties = $definitions->getCreateParameters();
                    $data = ['definition' => self::getDcat($dataObj->definition), 'properties' => $properties];
                    break;
                case 'INSPIRE':
                    $view = 'dataset.inspire';
                    $definitions = \App::make('Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface');
                    $properties = $definitions->getCreateParameters();
                    $data = ['definition' => self::getDcat($dataObj->definition), 'properties' => $properties];
                    break;
                default:
                    if ($dataObj->is_semantic) {
                        // This data object is always semantic
                        $view = 'dataset.turtle';

                        // Check if a configuration is given
                        $conf = array();

                        if (!empty($dataObj->semantic->conf)) {
                            $conf = $dataObj->semantic->conf;
                        }

                        $data = $dataObj->data->serialise('turtle');
                    } else {
                        $view = 'dataset.code';
                        $data = self::displayTree($dataObj->data);
                    }

                    break;
            }

        } elseif ($dataObj->is_semantic) {
            // The data object can be semantic without a specified source type
            $view = 'dataset.code';
            $data = $dataObj->data->serialise('turtle');

        } else {
            // Collection view
            $view = 'dataset.collection';
            $data = $dataObj->data;
        }


        // Gather meta-data to inject as a JSON-LD document so it can be picked up by search engines
        $definition = $dataObj->definition;

        $uri = \Request::root();
        $graph = new Graph();

        // Create the dataset uri
        $dataset_uri = $uri . "/" . $definition['collection_uri'] . "/" . $definition['resource_name'];
        $dataset_uri = str_replace(' ', '%20', $dataset_uri);

        // Add the dataset resource and its description
        $graph->addResource($dataset_uri, 'a', 'schema:Dataset');

        // Add the title to the dataset resource of the catalog
        if (!empty($definition['title'])) {
            $graph->addLiteral($dataset_uri, 'schema:headline', $definition['title']);
        }

        // Add the description, identifier, issues, modified of the dataset
        $graph->addLiteral($dataset_uri, 'schema:description', @$definition['description']);
        $graph->addLiteral($dataset_uri, 'schema:dateCreated', date(\DateTime::ISO8601, strtotime($definition['created_at'])));
        $graph->addLiteral($dataset_uri, 'schema:dateModified', date(\DateTime::ISO8601, strtotime($definition['updated_at'])));

        // Add the publisher resource to the dataset
        if (!empty($definition['publisher_name']) && !empty($definition['publisher_uri'])) {
            $graph->addResource($dataset_uri, 'schema:publisher', $definition['publisher_uri']);
        }

        // Optional dct terms
        $optional = array('date', 'language');
        $languages = \App::make('Tdt\Core\Repositories\Interfaces\LanguageRepositoryInterface');
        $licenses = \App::make('Tdt\Core\Repositories\Interfaces\LicenseRepositoryInterface');

        foreach ($optional as $dc_term) {
            if (!empty($definition[$dc_term])) {
                if ($dc_term == 'language') {
                    $lang = $languages->getByName($definition[$dc_term]);

                    if (!empty($lang)) {
                        $graph->addResource($dataset_uri, 'schema:inLanguage', 'http://lexvo.org/id/iso639-3/' . $lang['lang_id']);
                    }
                } else {
                    $graph->addLiteral($dataset_uri, 'schema:datasetTimeInterval', $definition[$dc_term]);
                }
            }
        }

        // Add the distribution of the dataset for SEO
        $format = '.json';

        if ($definition['source_type'] == 'ShpDefinition' || $dataObj->source_definition['type'] == 'KML') {
            $format = '.geojson';
        }

        $dataDownload = $graph->newBNode();

        $graph->addResource($dataset_uri, 'schema:distribution', $dataDownload);
        $graph->addResource($dataDownload, 'a', 'schema:DataDownload');
        $graph->addResource($dataDownload, 'schema:contentUrl', $dataset_uri . $format);

        // Add the license to the distribution
        if (!empty($definition['rights'])) {
            $license = $licenses->getByTitle($definition['rights']);

            if (!empty($license) && !empty($license['url'])) {
                $graph->addResource($dataset_uri, 'schema:license', $license['url']);
            }

            if (!empty($license)) {
                $dataObj->definition['rights_uri'] = $license['url'];
            }
        }

        $jsonld = $graph->serialise('jsonld');

        // Render the view
        return \View::make($view)->with('title', 'Dataset: '. $dataObj->definition['collection_uri'] . "/" . $dataObj->definition['resource_name'] . ' | The Datatank')
        ->with('body', $data)
        ->with('page_title', $dataObj->definition['collection_uri'] . "/" . $dataObj->definition['resource_name'])
        ->with('definition', $dataObj->definition)
        ->with('paging', $dataObj->paging)
        ->with('source_definition', $dataObj->source_definition)
        ->with('formats', $dataObj->formats)
        ->with('dataset_link', $dataset_link)
        ->with('prev_link', $prev_link)
        ->with('next_link', $next_link)
        ->with('query_string', $query_string)
        ->with('json_ld', $jsonld);
    }

    public static function getDocumentation()
    {
        return "The HTML formatter is a formatter which prints output for humans. It prints everything in the internal object and extra links towards meta-data and documentation.";
    }

    private static function displayTree($data, $format = 'json')
    {
        if ($format == 'json') {
            if (is_object($data)) {
                $data = get_object_vars($data);
            }

            if (defined('JSON_PRETTY_PRINT')) {
                $formattedJSON = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                $formattedJSON = json_encode($data);
            }

            return str_replace("\/", "/", $formattedJSON);
        } elseif ($format == 'xml') {
            return self::xmlPrettify($data, true);
        } else {
            \App::abort('400', "The requested format ($format) is not supported.");
        }
    }

    /**
     * Prettifies an XML string into a human-readable form
     *
     * @param string $xml The XML as a string
     * @param boolean $html_output True if the output should be escaped (for use in HTML)
     *
     * @return string
     */
    private static function getDcat($definition)
    {
        $repo = \App::make('Tdt\Core\Repositories\Interfaces\DcatRepositoryInterface');
        return $repo->getDcatDocument([$definition], $definition);
    }

    /**
     * Prettifies an XML string into a human-readable form
     *
     * @param string $xml The XML as a string
     * @param boolean $html_output True if the output should be escaped (for use in HTML)
     *
     * @return string
     */
    private static function xmlPrettify($xml)
    {
        $xml_obj = new \SimpleXMLElement($xml);
        $level = 4;
        $level = 4;
        $indent = 0;
        $pretty = array();

        $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

        if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
            $pretty[] = array_shift($xml);
        }

        foreach ($xml as $el) {
            if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
                $pretty[] = str_repeat(' ', $indent) . $el;
                $indent += $level;
            } else {
                if (preg_match('/^<\/.+>$/', $el)) {
                    $indent -= $level;
                }
                if ($indent < 0) {
                    $indent += $level;
                }
                $pretty[] = str_repeat(' ', $indent) . $el;
            }
        }
        $xml = implode("\n", $pretty);
        return htmlentities($xml);
    }
}

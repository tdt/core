<?php

namespace Tdt\Core\Datasets;

use Tdt\Core\Auth\Auth;
use Tdt\Core\Cache\Cache;
use Tdt\Core\ContentNegotiator;
use Tdt\Core\Definitions\DefinitionController;
use Tdt\Core\DataControllers\ADataController;
use Tdt\Core\Pager;
use Tdt\Core\ApiController;
use Tdt\Core\Formatters\FormatHelper;
use EasyRdf\RdfNamespace;

/**
 *  DatasetController
 *
 * @author Michiel Vancoillie <michiel@okfn.be>
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 */
class DatasetController extends ApiController
{
    /**
     * Retrieve a Data object identified by $uri
     *
     * @param string $uri The identifier that identifies a resource
     *
     * @return \Response
     */
    public function get($uri)
    {
        // Check permissions
        Auth::requirePermissions('dataset.view');

        // Split for an (optional) extension
        list($uri, $extension) = self::processURI($uri);
        $extension = strtolower($extension);

        // Check for caching
        // Based on: URI / Rest parameters / Query parameters / Paging headers
        $cache_string = $uri;

        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $cache_string .= '/limit=' . $limit . 'offset=' . $offset;
        $omit = ['limit', 'offset', 'page', 'page_size'];

        $query_string_params = \Input::get();

        foreach ($query_string_params as $key => $val) {
            if (in_array($key, $omit)) {
                unset($query_string_params[$key]);
            }
        }

        $cache_string .= http_build_query($query_string_params);
        $cache_string = sha1($cache_string);

        if (Cache::has($cache_string)) {
            return ContentNegotiator::getResponse(Cache::get($cache_string), $extension);
        } else {
            // Get definition
            $definition = $this->definition->getByIdentifier($uri);

            if ($definition) {
                // Get source definition
                $source_definition = $this->definition->getDefinitionSource(
                    $definition['source_id'],
                    $definition['source_type']
                );

                if ($source_definition) {
                    $source_type = $source_definition['type'];

                    // KML can be formatted into different formats
                    if ($source_definition['type'] == 'XML' && $source_definition['geo_formatted'] == 1) {
                        $source_type == 'kml';
                        $source_definition['type'] = 'KML';
                    }

                    // Create the right datacontroller
                    $controller_class = 'Tdt\\Core\\DataControllers\\' . $source_type . 'Controller';
                    $data_controller = \App::make($controller_class);

                    // Get REST parameters
                    $uri_segments = explode('/', $uri);
                    $definition_segments = explode('/', $definition['collection_uri']);

                    array_push($definition_segments, $definition['resource_name']);
                    $rest_parameters = array_diff($uri_segments, $definition_segments);
                    $rest_parameters = array_values($rest_parameters);

                    $throttle_response = $this->applyThrottle($definition);

                    if (!empty($throttle_response)) {
                        return $throttle_response;
                    }

                    // Retrieve dataobject from datacontroller
                    $data = $data_controller->readData($source_definition, $rest_parameters);

                    // If the source type is XML, just return the XML contents, don't transform
                    if (strtolower($source_type) == 'xml' && $extension == 'xml') {
                        return $this->createXMLResponse($data->data);
                    } elseif (!$data->is_semantic && $extension == 'xml' && $source_type != 'xml') {
                        \App::abort(406, "The requested format for the datasource is not available.");
                    } elseif (strtolower($source_type) == 'xml' && !$data->geo_formatted &&!empty($extension) && $extension != 'xml') {
                        \App::abort(406, "The requested format for the datasource is not available.");
                    } elseif (strtolower($source_type) == 'xml' && $data->geo_formatted &&!empty($extension) && !in_array($extension, $data->preferred_formats)) {
                        \App::abort(406, "The requested format for the datasource is not available.");
                    }

                    $data->rest_parameters = $rest_parameters;

                    // REST filtering
                    if ($source_type != 'INSTALLED' && count($data->rest_parameters) > 0) {
                        $data->data = self::applyRestFilter($data->data, $data->rest_parameters);
                    }

                    // Semantic paging with the hydra voc
                    if ($data->is_semantic && !empty($data->paging)) {
                        RdfNamespace::set('hydra', 'http://www.w3.org/ns/hydra/core#');
                        $graph = $data->data;
                        $url = \URL::to($definition['collection_uri'] . '/' . $definition['resource_name']);

                        $request_url = \Request::url();
                        $graph->addResource($request_url, 'void:subset', $url);

                        foreach ($data->paging as $key => $val) {
                            $paged_url = $request_url . '?offset=' . $val[0] . '&limit=' . $val[1] . Pager::buildQuerystring();

                            switch ($key) {
                                case 'next':
                                    $graph->addResource($request_url, 'hydra:nextPage', $paged_url);
                                    break;
                                case 'previous':
                                    $graph->addResource($request_url, 'hydra:previousPage', $paged_url);
                                    break;
                                case 'last':
                                    $graph->addResource($request_url, 'hydra:lastPage', $paged_url);
                                    break;
                                case 'first':
                                    $graph->addResource($request_url, 'hydra:firstPage', $paged_url);
                                    break;
                            }
                        }

                        $graph->addResource($url, 'a', 'dcat:Dataset');

                        $title = null;
                        if (!empty($definition['title'])) {
                            $title = $definition['title'];
                        } else {
                            $title = $definition['collection_uri'] . '/' . $definition['resource_name'];
                        }

                        $graph->addLiteral($url, 'dc:title', $title);
                        $graph->addLiteral($url, 'dc:description', $source_definition['description']);
                        $graph->addResource($url, 'dcat:distribution', $url . '.json');

                        $data->data = $graph;
                    }

                    // Add definition to the object
                    $data->definition = $definition;

                    // Add source definition to the object
                    $data->source_definition = $source_definition;

                    // Add the available, supported formats to the object
                    $format_helper = new FormatHelper();
                    $data->formats = $format_helper->getAvailableFormats($data);

                    // Store in cache
                    Cache::put($cache_string, $data, $source_definition['cache']);

                    // Return the formatted response with content negotiation
                    return ContentNegotiator::getResponse($data, $extension);
                } else {
                    \App::abort(404, "Source for the definition could not be found.");
                }

            } else {
                // Coulnd't find a definition, but it might be a collection
                $resources = $this->definition->getByCollection($uri);

                if (count($resources) > 0) {
                    $data = new Data();
                    $data->data = new \stdClass();
                    $data->data->datasets = array();
                    $data->data->collections = array();

                    foreach ($resources as $res) {
                        // Check if it's a subcollection or a dataset
                        $collection_uri = rtrim($res['collection_uri'], '/');
                        if ($collection_uri == $uri) {
                            array_push($data->data->datasets, \URL::to($collection_uri . '/' . $res['resource_name']));
                        } else {
                            // Push the subcollection if it's not already in the array
                            if (!in_array(\URL::to($collection_uri), $data->data->collections)) {
                                array_push($data->data->collections, \URL::to($collection_uri));
                            }
                        }
                    }

                    // Fake a definition
                    $data->definition = new \Definition();
                    $uri_array = explode('/', $uri);
                    $last_chunk = array_pop($uri_array);

                    $data->definition->collection_uri = join('/', $uri_array);
                    $data->definition->resource_name = $last_chunk;

                    // Return the formatted response with content negotiation
                    return ContentNegotiator::getResponse($data, $extension);
                } else {
                    \App::abort(404, "The dataset or collection you were looking for could not be found (URI: $uri).");
                }
            }

        }
    }

    private function getRestParameters($uri, $definition)
    {

    }

    /**
     * Return a HEAD response indicating if a URI is reachable for the user agent
     *
     * @param string $uri The identifier that identifies a resource
     *
     * @return \Response
     */
    public function head($uri)
    {
        // Check permissions
        Auth::requirePermissions('dataset.view');

        // Split for an (optional) extension
        list($uri, $extension) = self::processURI($uri);

        // Get definition
        $definition = $this->definition->getByIdentifier($uri);

        $response = new \Response();

        if ($definition) {
            $response = \Response::make(null, 200);

        } else {
            $response = \Response::make(null, 404);
        }

        return $response;
    }

    /**
     * Process the URI and return the extension (=format) and the resource identifier URI
     *
     * @param string $uri The URI that has been passed
     * @return array
     */
    private static function processURI($uri)
    {
        $dot_position = strrpos($uri, '.');

        if (!$dot_position) {
            return array($uri, null);
        }

        // If a dot has been found, do a couple
        // of checks to find out if it introduces a formatter
        $uri_parts = explode('.', $uri);

        $possible_extension = strtoupper(array_pop($uri_parts));

        $uri = implode('.', $uri_parts);

        $formatter_class = 'Tdt\\Core\\Formatters\\' . $possible_extension . 'Formatter';

        if (!class_exists($formatter_class)) {
            // Re-attach the dot with the latter part of the uri
            $uri .= '.' . strtolower($possible_extension);

            return array($uri, null);
        }

        return array($uri, strtolower($possible_extension));
    }

    /**
     * Apply RESTful filtering of the data (case insensitive)
     *
     * @param mixed $data        The data to be filtered
     * @param array $rest_params The data to be filtered
     *
     * @return mixed filtered object
     */
    private static function applyRestFilter($data, $rest_params)
    {
        foreach ($rest_params as $rest_param) {
            if (!empty($rest_param)) {
                if (is_object($data) && $key = self::propertyExists($data, $rest_param)) {
                    $data = $data->$key;
                } elseif (is_array($data)) {
                    if ($key = self::keyExists($data, $rest_param)) {
                        $data = $data[$key];
                    } elseif (is_numeric($rest_param)) {
                        for ($i = 0; $i <= $rest_param; $i++) {
                            $result = array_shift($data);
                        }

                        $data = $result;
                    } else {
                        \App::abort(404, "No property ($rest_param) has been found.");
                    }
                } else {
                    \App::abort(404, "No property ($rest_param) has been found.");
                }
            }
        }

        return array($data);
    }

    /**
     * Check if a uri resembles a definition, if so return a data object
     *
     * @param string $identifier The identifier of a resource
     *
     * @return Tdt\Core\Datasets\Data
     */
    public static function fetchData($identifier)
    {
        // Retrieve the definition
        $definition_repo = \App::make('Tdt\\Core\\Repositories\\Interfaces\\DefinitionRepositoryInterface');
        $definition = $definition_repo->getByIdentifier($identifier);

        if ($definition) {
            // Get the source definition
            $source_definition = $definition_repo->getDefinitionSource($definition['source_id'], $definition['source_type']);

            if ($source_definition) {
                // Create the correct datacontroller
                $controller_class = 'Tdt\\Core\\DataControllers\\' . $source_definition['type'] . 'Controller';
                $data_controller = \App::make($controller_class);

                // Get REST parameters
                $uri = \Request::path();
                list($uri, $extension) = self::processURI($uri);

                $uri_segments = explode('/', $uri);
                $definition_segments = explode('/', $definition['collection_uri']);
                array_push($definition_segments, $definition['resource_name']);
                $rest_parameters = array_diff($uri_segments, $definition_segments);
                $rest_parameters = array_values($rest_parameters);

                // Retrieve dataobject from datacontroller
                $data = $data_controller->readData($source_definition, $rest_parameters);
                $data->rest_parameters = $rest_parameters;

                // REST filtering
                if ($source_definition['type'] != 'INSTALLED' && count($data->rest_parameters) > 0) {
                    $data->data = self::applyRestFilter($data->data, $data->rest_parameters);
                }

                return $data;
            } else {
                \App::abort(404, "Source for the definition could not be found.");
            }
        } else {
            \App::abort(404, "The definition could not be found.");
        }
    }

    /**
     * Case insensitive search for a property of an object
     *
     * @param stdClass $object   The object submissive to be searched
     * @param string   $property The property name that is looked for in the $object
     *
     * @return boolean
     */
    private static function propertyExists($object, $property)
    {

        $vars = get_object_vars($object);

        foreach ($vars as $key => $value) {
            if (strtolower($property) == strtolower($key)) {
                return $key;
                break;
            }
        }
        return false;
    }

    /**
     * Case insensitive search for a key in an array
     *
     * @param array  $array    The array that undergoes the search
     * @param string $property The property that is looked for in the array
     *
     * @return boolean
     */
    private static function keyExists($array, $property)
    {

        foreach ($array as $key => $value) {
            if (strtolower($property) == strtolower($key)) {
                return $key;
                break;
            }
        }
        return false;
    }

    /**
     * Return an XML response
     *
     * @return \Response
     */
    public function createXMLResponse($data)
    {
        // Create response
        $response = \Response::make($data, 200);

        // Set headers
        return $response->header('Content-Type', 'text/xml;charset=UTF-8');
    }

    /**
     * Throttle on the basis of source type
     *
     * @param array $definition
     *
     * @return Response
     */
    private function applyThrottle($definition)
    {
        if ($definition['source_type'] == 'ElasticsearchDefinition') {
            $requestsPerHour = 720;

            // Rate limit by IP address
            $key = sprintf('api:%s', \Request::getClientIp());

            // Add if doesn't exist
            // Remember for 1 hour
            \Cache::add($key, 0, 60);

            // Add to count
            $count = \Cache::get($key);

            if ($count > $requestsPerHour) {
                $response = \Response::make('', 429);
                $response->setContent('Rate limit exceeded, maximum of ' . $requestsPerHour . ' requests per hour has been reached.');

                return $response;
            } else {
                \Cache::increment($key);
            }
        }
    }
}

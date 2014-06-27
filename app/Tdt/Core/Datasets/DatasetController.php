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
        list($uri, $extension) = $this->processURI($uri);

        // Check for caching
        // Based on: URI / Rest parameters / Query parameters / Paging headers
        $cache_string = $uri;

        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $cache_string .= '/limit=' . $limit . 'offset=' . $offset;
        $cache_string .= http_build_query(\Input::except('limit', 'offset', 'page', 'page_size'));
        $cache_string = sha1($cache_string);

        if (Cache::has($cache_string)) {
            return ContentNegotiator::getResponse(Cache::get($cache_string), $extension);
        } else {

            // Get definition
            $definition = $this->definition->getByIdentifier($uri);

            if ($definition) {

                // Get source definition
                $source_definition = $this->definition->getDefinitionSource($definition['source_id'], $definition['source_type']);

                if ($source_definition) {

                    $source_type = $source_definition['type'];

                    // Create the right datacontroller
                    $controller_class = 'Tdt\\Core\\DataControllers\\' . $source_type . 'Controller';
                    $data_controller = \App::make($controller_class);

                    // Get REST parameters
                    $rest_parameters = str_replace($definition['collection_uri'] . '/' . $definition['resource_name'], '', $uri);
                    $rest_parameters = ltrim($rest_parameters, '/');
                    $rest_parameters = explode('/', $rest_parameters);

                    if (empty($rest_parameters[0]) && !is_numeric($rest_parameters[0])) {
                        $rest_parameters = array();
                    }

                    // Retrieve dataobject from datacontroller
                    $data = $data_controller->readData($source_definition, $rest_parameters);

                    $data->rest_parameters = $rest_parameters;

                    // REST filtering
                    if ($source_type != 'INSTALLED' && count($data->rest_parameters) > 0) {
                        $data->data = self::applyRestFilter($data->data, $data->rest_parameters);
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
        list($uri, $extension) = $this->processURI($uri);

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
    private function processURI($uri)
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
            $uri .= '.' . $possible_extension;

            return array($uri, null);
        }

        return array($uri, $possible_extension);
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
                $rest_parameters = str_replace($definition['collection_uri'] . '/' . $definition['resource_name'], '', $identifier);
                $rest_parameters = ltrim($rest_parameters, '/');
                $rest_parameters = explode('/', $rest_parameters);

                if (empty($rest_parameters[0]) && !is_numeric($rest_parameters[0])) {
                    $rest_parameters = array();
                }

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
}

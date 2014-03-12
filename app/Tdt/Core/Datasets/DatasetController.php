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
 * DatasetController
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DatasetController extends ApiController
{

    public function get($uri)
    {
        // Set permission
        Auth::requirePermissions('dataset.view');

        // Split for an (optional) extension
        preg_match('/([^\.]*)(?:\.(.*))?$/', $uri, $matches);

        // URI is always the first match
        $uri = $matches[1];

        // Get extension (if set)
        $extension = (!empty($matches[2]))? $matches[2]: null;

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
                    $data_controller = new $controller_class();

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
                        $collection_uri = rtrim($res->collection_uri, '/');
                        if ($collection_uri == $uri) {
                            array_push($data->data->datasets,  \URL::to($collection_uri . '/' . $res->resource_name));
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
     * Apply RESTful filtering of the data (case insensitive)
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
     * Check if a uri resembles a definition, if so return the data
     */
    public static function fetchData($uri)
    {

        // Retrieve the definition
        $definition_repo = \App::make('Tdt\\Core\\Repositories\\Interfaces\\DefinitionRepositoryInterface');
        $definition = $definition_repo->getByIdentifier($uri);

        if ($definition) {

            // Get the source definition
            $source_definition = $definition_repo->getDefinitionSource($definition['source_id'], $definition['source_type']);

            if ($source_definition) {

                // Create the correct datacontroller
                $controller_class = 'Tdt\\Core\\DataControllers\\' . $source_definition['type'] . 'Controller';
                $data_controller = new $controller_class();

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

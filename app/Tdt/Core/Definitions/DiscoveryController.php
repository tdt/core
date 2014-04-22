<?php

namespace Tdt\Core\Definitions;

use Tdt\Core\Auth\Auth;
use Tdt\Core\Datasets\Data;
use Tdt\Core\ContentNegotiator;

use Tdt\Core\ApiController;

/**
 * DiscoveryController
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DiscoveryController extends ApiController
{

    public function get($uri = null)
    {

        // Set permission
        Auth::requirePermissions('discovery.view');

        $discovery_document = self::createDiscoveryDocument();

        // If the input package is installed, add it to the discovery document
        if (class_exists('Tdt\Input\Controllers\DiscoveryController')) {
            $discovery_class = 'tdt\input\controllers\DiscoveryController';
            $discovery_document->resources->input = $discovery_class::createDiscoveryDocument();
        }

        // If the triples package is installed, add it to the discovery document
        if (class_exists('Tdt\Triples\Controllers\DiscoveryController')) {
            $discovery_class = 'Tdt\Triples\Controllers\DiscoveryController';
            $discovery_document->resources->triples = $discovery_class::createDiscoveryDocument();
        }

        return self::makeResponse(str_replace("\/", "/", json_encode($discovery_document)));
    }

    /**
     * Create the discovery document
     */
    public function createDiscoveryDocument()
    {

        // Create and return a dument that holds a self-explanatory document
        // about how to interface with the datatank
        $discovery_document = new \stdClass();

        // Create the discovery dument head properties
        $discovery_document->protocol = "rest";
        $discovery_document->rootUrl = \URL::to('/api');
        $discovery_document->version = \Config::get('app.version');
        $discovery_document->resources = new \stdClass();

        $discovery_document->resources->definitions = self::createDefinitions();
        $discovery_document->resources->info = self::createInfo();
        $discovery_document->resources->dcat = self::createDcat();
        $discovery_document->resources->languages = self::createLanguages();
        $discovery_document->resources->licenses = self::createLicenses();
        $discovery_document->resources->prefixes = self::createPrefixes();

        return $discovery_document;
    }

    /**
     * Create the definitions resource for the discovery document
     */
    private function createDefinitions()
    {

        $definitions = new \stdClass();

        $methods = new \stdClass();

        // Attach the methods to the up the methods object
        $methods->get = self::createDefGetDiscovery();
        $methods->put = self::createDefPutDiscovery();
        $methods->delete = self::createDefDeleteDiscovery();
        $methods->patch = self::createDefPatchDiscovery();

        // Attach the methods to the definitions object
        $definitions->methods = $methods;

        return $definitions;
    }

    /**
     * Create the get discovery documentation.
     */
    private function createDefGetDiscovery()
    {

        $get = new \stdClass();

        $get->httpMethod = "GET";
        $get->path = "/definitions/{identifier}";
        $get->description = "Get a resource definition identified by the {identifier} value, or retrieve a list of the current definitions by leaving {identifier} empty.";

        return $get;
    }

    /**
     * Create the put discovery documentation.
     */
    private function createDefPutDiscovery()
    {

        $put = new \stdClass();

        $put->httpMethod = "PUT";
        $put->path = "/definitions/{identifier}";
        $put->description = "Add a resource definition identified by the {identifier} value. The {identifier} consists of 1 or more collection identifiers, followed by a final resource name. (e.g. world/demography/2013/seniors). Valid characters that can be used are alphanumerical, underscores and whitespaces.";
        $put->contentType = "application/tdt.definition+json";

        // Every type of definition is identified by a certain mediatype
        $put->body = new \stdClass();

        // Get the base properties that can be added to every definition
        $base_properties = $this->definition->getCreateParameters();

        // Fetch all the supported definition models by iterating the models directory
        if ($handle = opendir(app_path() . '/models/sourcetypes')) {
            while (false !== ($entry = readdir($handle))) {

                if (preg_match("/(.+)Definition\.php/i", $entry, $matches)) {

                    $source_repository = 'Tdt\\Core\\Repositories\\Interfaces\\' . ucfirst(strtolower($matches[1])) . "DefinitionRepositoryInterface";
                    $source_repository = \App::make($source_repository);

                    $definition_type = strtolower($matches[1]);

                    if (method_exists($source_repository, 'getAllParameters')) {

                        $put->body->$definition_type = new \stdClass();
                        $put->body->$definition_type->description = "Create a definition that allows for publication of data inside a $matches[1] datastructure.";

                        // Add the required type parameter
                        $type = array(
                            'type' => array(
                                'required' => true,
                                'name' => 'Type',
                                'description' => 'The type of the data source.',
                                'type' => 'string',
                                'value' => $definition_type
                            )
                        );

                        $all_properties = array_merge($type, $source_repository->getAllParameters(), $base_properties);

                        // Fetch the Definition properties, and the SourceType properties, the latter also contains relation properties e.g. TabularColumn properties
                        $put->body->$definition_type->parameters = $all_properties;
                    }
                }
            }
            closedir($handle);
        }

        return $put;
    }

    /**
     * Create the delete discovery documentation.
     */
    private function createDefDeleteDiscovery()
    {

        $delete = new \stdClass();

        $delete->httpMethod = "DELETE";
        $delete->path = "/definitions/{identifier}";
        $delete->description = "Delete a resource definition identified by the {identifier} value.";

        return $delete;
    }

    /**
     * Create the patch discovery documentation.
     */
    private function createDefPatchDiscovery()
    {

        $patch = new \stdClass();

        $patch->httpMethod = "PATCH";
        $patch->path = "/definitions/{identifier}";
        $patch->description = "Patch a resource definition identified by the {identifier} value. In contrast to PUT, there's no need to pass the media type in the headers.";

        // Every type of definition is identified by a certain mediatype (source type)
        $patch->body = new \stdClass();

        // Get the base properties that can be added to every definition
        $base_properties = $this->definition->getCreateParameters();

        // Fetch all the supported definition models by iterating the models directory
        if ($handle = opendir(app_path() . '/models/sourcetypes')) {
            while (false !== ($entry = readdir($handle))) {

                if (preg_match("/(.+)Definition\.php/i", $entry, $matches)) {

                    $source_repository = 'Tdt\\Core\\Repositories\\Interfaces\\' . ucfirst(strtolower($matches[1])) . "DefinitionRepositoryInterface";
                    $source_repository = \App::make($source_repository);

                    $definition_type = strtolower($matches[1]);

                    if (method_exists($source_repository, 'getAllParameters')) {

                        $patch->body->$definition_type = new \stdClass();
                        $patch->body->$definition_type->description = "Patch an existing definition.";

                        $all_properties = array_merge($source_repository->getAllParameters(), $base_properties);

                        foreach ($all_properties as $key => $info) {
                            unset($all_properties[$key]['required']);
                        }

                        // Fetch the Definition properties, and the SourceType properties, the latter also contains relation properties e.g. TabularColumn properties
                        $patch->body->$definition_type->parameters = $all_properties;
                    }
                }
            }
            closedir($handle);
        }

        return $patch;
    }

    /**
     * Create the info discovery documentation
     */
    private function createInfo()
    {

        // Info only supports the get method
        $info = new \stdClass();

        // Attach the methods to the info object
        $info->methods = new \stdClass();
        $info->methods->get  = new \stdClass();

        $info->methods->get->httpMethod = "GET";
        $info->methods->get->path = "/info";
        $info->methods->get->description = "Get a list of all retrievable datasets published on this datatank instance.";

        return $info;
    }

    /**
     * Create the dcat discovery documentation
     */
    private function createDcat()
    {

        // Dcat only supports the get method
        $dcat = new \stdClass();

        // Attach the methods to the dcat object
        $dcat->methods = new \stdClass();
        $dcat->methods->get  = new \stdClass();

        $dcat->methods->get->httpMethod = "GET";
        $dcat->methods->get->path = "/dcat";
        $dcat->methods->get->description = "Get a list of all retrievable datasets published on this datatank instance in a DCAT vocabulary. In contrast with all the other resources, this data will be returned in a turtle serialization.";

        return $dcat;
    }

    /**
     * Create the languages discovery documentation
     */
    private function createLanguages()
    {

        // Languages only supports the get method
        $languages = new \stdClass();

        // Attach the discovery information
        $languages->methods = new \stdClass();
        $languages->methods->get = new \stdClass();

        $languages->methods->get->httpMethod = 'GET';
        $languages->methods->get->path = '/languages';
        $languages->methods->get->description = 'Get a list of supported languages that are made available to use as DCAT meta-data.';

        return $languages;
    }

    /**
     * Create the licenses discovery documentation
     */
    private function createLicenses()
    {

        // Licenses only supports the get method
        $licenses = new \stdClass();

        // Attach the discovery information
        $licenses->methods = new \stdClass();
        $licenses->methods->get = new \stdClass();

        $licenses->methods->get->httpMethod = 'GET';
        $licenses->methods->get->path = '/licenses';
        $licenses->methods->get->description = 'Get a list of supported licenses that are made available to use as DCAT meta-data.';

        return $licenses;
    }

    /**
     * Create the prefixes discovery documentation
     */
    private function createPrefixes()
    {

        // Prefixes only supports the get method
        $prefixes = new \stdClass();

        // Attach the discovery information
        $prefixes->methods = new \stdClass();
        $prefixes->methods->get = new \stdClass();

        $prefixes->methods->get->httpMethod = 'GET';
        $prefixes->methods->get->path = '/prefixes';
        $prefixes->methods->get->description = "Get a list of supported prefixes and uri's that are used to pass along with semantic data results.";

        return $prefixes;
    }

    /**
     * Return the response with the given data (formatted in json)
     */
    private function makeResponse($data)
    {

         // Create response
        $response = \Response::make($data, 200);

        // Set headers
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

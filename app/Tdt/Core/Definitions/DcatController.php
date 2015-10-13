<?php

namespace Tdt\Core\Definitions;

use Illuminate\Routing\Router;

use Tdt\Core\Auth\Auth;
use Tdt\Core\Datasets\Data;
use Tdt\Core\ContentNegotiator;
use Tdt\Core\Pager;
use Tdt\Core\ApiController;
use Tdt\Core\Repositories\Interfaces\LicenseRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\LanguageRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\DcatRepositoryInterface;
use Tdt\Core\Repositories\Interfaces\SettingsRepositoryInterface;

/**
 * DcatController
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DcatController extends ApiController
{
    public function __construct(
        LanguageRepositoryInterface $languages,
        LicenseRepositoryInterface $licenses,
        DefinitionRepositoryInterface $definitions,
        DcatRepositoryInterface $dcat,
        SettingsRepositoryInterface $settings
    ) {
        $this->languages = $languages;
        $this->licenses = $licenses;
        $this->definitions = $definitions;
        $this->dcat = $dcat;
        $this->settings = $settings;
    }

    public function get($uri)
    {
        // Ask permission
        Auth::requirePermissions('info.view');

        // Default format is ttl for dcat
        if (empty($extension)) {
            $extension = 'ttl';
        }

        $dcat = $this->createDcat();

        // Allow content nego. for dcat
        return ContentNegotiator::getResponse($dcat, $extension);
    }

    /**
     * Create the DCAT document of the published (non-draft) resources
     *
     * @return mixed \Data object with a graph of DCAT information
     */
    private function createDcat()
    {
        $ns = $this->dcat->getNamespaces();

        foreach ($ns as $prefix => $uri) {
            \EasyRdf_Namespace::set($prefix, $uri);
        }

        // If limit is empty, provide a custom page size for the DCAT document

        $limit = \Input::get('limit');
        if (empty($limit)) {
            \Input::merge(array('limit' => 100));
        }

        // Apply paging when fetching the definitions
        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $definition_count = $this->definitions->countPublished();

        $definitions = $this->definitions->getAllPublished($limit, $offset);

        $oldest = $this->definitions->getOldest();

        $describedDefinitions = array();

        // Add the source type description to the definition
        foreach ($definitions as $definition) {
            $definition = array_merge($definition, $this->definitions->getFullDescription($definition['collection_uri'] . '/' . $definition['resource_name']));
            array_push($describedDefinitions, $definition);
        }

        $graph = $this->dcat->getDcatDocument($describedDefinitions, $oldest);

        // Return the dcat feed in our internal data object
        $data_result = new Data();
        $data_result->data = $graph;
        $data_result->is_semantic = true;
        $data_result->paging = Pager::calculatePagingHeaders($limit, $offset, $definition_count);

        // Add the semantic configuration for the ARC graph
        $data_result->semantic = new \stdClass();
        $data_result->semantic->conf = array('ns' => $ns);
        $data_result->definition = new \stdClass();
        $data_result->definition->resource_name = 'dcat';
        $data_result->definition->collection_uri = 'info';

        return $data_result;
    }

    public function head($uri)
    {
        $response =  \Response::make(null, 200);

        // Set headers
        $response->header('Content-Type', 'text/turtle;charset=UTF-8');
        $response->header('Pragma', 'public');

        // Return formatted response
        return $response;
    }
}

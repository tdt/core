<?php

/**
 * The datasetcontroller
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace Tdt\Core\Ui;

use Tdt\Core\Auth\Auth;

class DatasetController extends UiController
{

    /**
     * Admin.dataset.view
     */
    public function getIndex()
    {

        // Set permission
        Auth::requirePermissions('admin.dataset.view');

        // Get all definitions
        $definitions = \Definition::all();

        return \View::make('ui.datasets.list')
                    ->with('title', 'Dataset management | The Datatank')
                    ->with('definitions', $definitions);
    }

    /**
     * Admin.dataset.create
     */
    public function getAdd()
    {

        // Set permission
        Auth::requirePermissions('admin.dataset.create');

        $discovery = $this->getDiscoveryDocument();

        // Get spec for media types
        $mediatypes_spec = $discovery->resources->definitions->methods->put->body;

        // Sort parameters for each media type
        $mediatypes = array();
        $lists = array();
        foreach ($mediatypes_spec as $mediatype => $type) {

            $parameters_required = array();
            $parameters_optional = array();
            $parameters_dc = array();
            $parameters_columns = array();
            $parameters_geo = array();

            foreach ($type->parameters as $parameter => $object) {

                // Filter array type parameters

                if (empty($object->parameters)) {

                    // Filter Dublin core parameters
                    if (!empty($object->group) && $object->group == 'dc') {

                        // Fetch autocomplete DC fields
                        if ($object->type == 'list') {
                            $uri = $object->list;

                            // Check list cache
                            if (empty($lists[$uri])) {
                                $data = json_decode($this->getDocument($uri));
                                $data_set = array();

                                foreach ($data as $o) {
                                    if (!empty($o->{$object->list_option})) {
                                        $data_set[] = $o->{$object->list_option};
                                    }
                                }

                                $lists[$uri] = $data_set;
                            }

                            $object->list = $lists[$uri];

                        }


                        $parameters_dc[$parameter] = $object;

                    } else {
                        // Fitler optional vs required
                        if ($object->required) {
                            // Filter the type paramter
                            if ($parameter != 'type') {
                                $parameters_required[$parameter] = $object;
                            }
                        } else {
                            $parameters_optional[$parameter] = $object;
                        }
                    }
                } else {


                    switch ($parameter) {
                        case 'columns':
                            foreach ($object->parameters as $param => $obj) {
                                $parameters_columns[$param] = $obj;
                            }
                            break;
                        case 'geo':
                            foreach ($object->parameters as $param => $obj) {
                                $parameters_geo[$param] = $obj;
                            }
                            break;
                    }

                }
            }

            // Filter on unnecessary optional parameters
            unset($parameters_optional['cache_minutes']);

            // TODO special treatment for caching
            unset($parameters_optional['draft']);

            $mediatypes[$mediatype]['parameters_required'] = $parameters_required;
            $mediatypes[$mediatype]['parameters_optional'] = $parameters_optional;
            $mediatypes[$mediatype]['parameters_dc'] = $parameters_dc;
            $mediatypes[$mediatype]['parameters_columns'] = $parameters_columns;
            $mediatypes[$mediatype]['parameters_geo'] = $parameters_geo;
        }

        return \View::make('ui.datasets.add')
                    ->with('title', 'Add a dataset | The Datatank')
                    ->with('mediatypes', $mediatypes);

        return \Response::make($view);
    }


    /**
     * Admin.dataset.update
     */
    public function getEdit($id)
    {

        // Set permission
        Auth::requirePermissions('admin.dataset.update');

        $definition = \Definition::find($id);
        if ($definition) {

            // Get source defintion
            $source_definition = $definition->source()->first();

            $discovery = $this->getDiscoveryDocument();

            // Get spec for media type
            if (empty($discovery->resources->definitions->methods->patch->body->{strtolower($source_definition->type)})) {
                \App::abort('500', 'There is no definition of the media type of this dataset in the discovery document.');
            }
            $mediatype = $discovery->resources->definitions->methods->patch->body->{strtolower($source_definition->type)};

            // Sort parameters
            $parameters_required = array();
            $parameters_optional = array();
            $parameters_dc = array();
            $lists = array();

            foreach ($mediatype->parameters as $parameter => $object) {

                // Filter array type parameters
                if (empty($object->parameters)) {

                    // Filter Dublin core parameters
                    if (!empty($object->group) && $object->group == 'dc') {

                        // Fetch autocomplete DC fields
                        if ($object->type == 'list') {
                            $uri = $object->list;

                            // Check list cache
                            if (empty($lists[$uri])) {

                                $data = json_decode($this->getDocument($uri));
                                $data_set = array();

                                foreach ($data as $o) {
                                    if (!empty($o->{$object->list_option})) {
                                        $data_set[] = $o->{$object->list_option};
                                    }
                                }

                                $lists[$uri] = $data_set;
                            }

                            $object->list = $lists[$uri];

                        }

                        $parameters_dc[$parameter] = $object;
                    } else {
                        // Filter optional vs required
                        $parameters_optional[$parameter] = $object;
                    }
                }

            }

            // Filter on unnecessary optional parameters
            unset($parameters_optional['cache_minutes']);

            // TODO special treatment for draft
            unset($parameters_optional['draft']);

            return \View::make('ui.datasets.edit')
                        ->with('title', 'Edit a dataset | The Datatank')
                        ->with('definition', $definition)
                        ->with('mediatype', $mediatype)
                        ->with('parameters_required', $parameters_required)
                        ->with('parameters_optional', $parameters_optional)
                        ->with('parameters_dc', $parameters_dc)
                        ->with('source_definition', $source_definition);

            return \Response::make($view);

        } else {
            return \Redirect::to('api/admin/datasets');
        }
    }

    /**
     * Admin.dataset.delete
     */
    public function getDelete($id)
    {

        // Set permission
        Auth::requirePermissions('admin.dataset.delete');

        if (is_numeric($id)) {
            $definition = \Definition::find($id);
            if ($definition) {
                // Delete it (with cascade)
                $definition->delete();
            }
        }

        return \Redirect::to('api/admin/datasets');
    }

    private function getDiscoveryDocument()
    {
        // Create a CURL client
        $cURL = new \Buzz\Client\Curl();
        $cURL->setVerifyPeer(false);
        $cURL->setTimeout(30);

        // Get discovery document
        $browser = new \Buzz\Browser($cURL);
        $response = $browser->get(\URL::to('discovery'));

        // Document content
        $discovery = json_decode($response->getContent());

        return $discovery;
    }

    private function getDocument($uri)
    {
        // Create a CURL client
        $cURL = new \Buzz\Client\Curl();
        $cURL->setVerifyPeer(false);
        $cURL->setTimeout(30);

        // Get discovery document
        $browser = new \Buzz\Browser($cURL);
        $response = $browser->get(\URL::to($uri));

        // Document content
        return $response->getContent();
    }
}

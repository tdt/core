<?php

/**
 * The DatasetController: Takes care of the UI side of managing datasets.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 * @author Jan Vansteenlandt <jan@okfn.be>
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

        // Check user id
        $user = \Sentry::getUser();

        // Get created definitions
        $definitions = \Definition::where('user_id', $user->id)->get();

        // Get updated definitions
        $updatedDefinitions = \DB::table('definitions_updates')
            ->where('definitions_updates.user_id', $user->id)
            ->select('definitions_updates.definition_id')
            ->get();

        $updatedDefinitionIds = [];

        foreach ($updatedDefinitions as $updatedDefinition) {
            $updatedDefinitionIds[] = $updatedDefinition->definition_id;
        }

        $definitions_updated = null;

        if (! empty($updatedDefinitionIds)) {
            $definitions_updated = \Definition::whereIn('id', $updatedDefinitionIds)
                    ->get();
        }

        // Get other definitions
        $otherDefinitionsQuery = \Definition::where('user_id', '!=', $user->id);

        if (! empty($updatedDefinitionIds)) {
            $otherDefinitionsQuery->whereNotIn('id', $updatedDefinitionIds);
        }

        $definitions_others = $otherDefinitionsQuery->get();

        return \View::make('ui.datasets.list')
                    ->with('title', 'Dataset management (Created/Updated/Others) | The Datatank')
                    ->with('definitions', $definitions)
                    ->with('definitions_updated', $definitions_updated)
                    ->with('definitions_others', $definitions_others);
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
            $parameters_geodcat = array();

            foreach ($type->parameters as $parameter => $object) {
                // Filter array type parameters
                if (empty($object->parameters)) {
                    // Filter Dublin core parameters
                    if (! empty($object->group) && $object->group == 'dc') {
                        // Fetch autocomplete DC fields
                        if ($object->type == 'list') {
                            $uri = $object->list;

                            // Check list cache
                            if (empty($lists[$uri])) {
                                $data = json_decode($this->getDocument($uri));
                                $data_set = array();

                                foreach ($data as $o) {
                                    if (! empty($o->{$object->list_option})) {
                                        $data_set[] = $o->{$object->list_option};
                                    }
                                }

                                $lists[$uri] = $data_set;
                            }

                            $object->list = $lists[$uri];

                        }

                        $parameters_dc[$parameter] = $object;

                    } elseif (! empty($object->group) && $object->group == 'geodcat') {
                        // Filter Geo params
                        $parameters_geodcat[$parameter] = $object;
                    } else {
                        // Filter optional vs required
                        if ($object->type == 'list' && (strpos($object->list, '|') !== false)) {
                            $object->list = explode('|', $object->list);
                        } elseif ($object->type == 'list') {
                            $uri = $object->list;

                            // Check list cache
                            if (empty($lists[$uri])) {
                                $data = json_decode($this->getDocument($uri));
                                $data_set = array();

                                foreach ($data as $o) {
                                    if (! empty($o->{$object->list_option})) {
                                        $data_set[] = $o->{$object->list_option};
                                    }
                                }

                                $lists[$uri] = $data_set;
                            }

                            $object->list = $lists[$uri];
                        }

                        if ($object->required) {
                            // Filter the type parameter
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
            unset($parameters_optional['draft_flag']);
            unset($parameters_required['username']);
            unset($parameters_required['user_id']);
            unset($parameters_optional['job_id']);

            // Translate the parameters
            $parameters_required = $this->translateParameters($parameters_required, $mediatype);
            $parameters_optional = $this->translateParameters($parameters_optional, $mediatype);
            $parameters_dc = $this->translateParameters($parameters_dc, 'definition');
            $parameters_columns = $this->translateParameters($parameters_columns, $mediatype);
            $parameters_geo = $this->translateParameters($parameters_geo, $mediatype);
            $parameters_geodcat = $this->translateParameters($parameters_geodcat, 'geodcat');

            $mediatypes[$mediatype]['parameters_required'] = $parameters_required;
            $mediatypes[$mediatype]['parameters_optional'] = $parameters_optional;
            $mediatypes[$mediatype]['parameters_dc'] = $parameters_dc;
            $mediatypes[$mediatype]['parameters_columns'] = $parameters_columns;
            $mediatypes[$mediatype]['parameters_geo'] = $parameters_geo;
            $mediatypes[$mediatype]['parameters_geodcat'] = $parameters_geodcat;
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
            $parameters_geodcat = array();
            $lists = array();

            foreach ($mediatype->parameters as $parameter => $object) {
                // Filter array type parameters
                if (empty($object->parameters)) {
                    // Filter Dublin core parameters
                    if (! empty($object->group) && $object->group == 'dc') {
                        // Fetch autocomplete DC fields
                        if ($object->type == 'list') {
                            $uri = $object->list;

                            // Check list cache
                            if (empty($lists[$uri])) {
                                $data = json_decode($this->getDocument($uri));
                                $data_set = array();

                                foreach ($data as $o) {
                                    if (! empty($o->{$object->list_option})) {
                                        $data_set[] = $o->{$object->list_option};
                                    }
                                }

                                $lists[$uri] = $data_set;
                            }

                            $object->list = $lists[$uri];

                        }

                        $parameters_dc[$parameter] = $object;
                    } elseif (! empty($object->group) && $object->group == 'geodcat') {
                        // Filter Geo params
                        $parameters_geodcat[$parameter] = $object;
                    } else {
                        // Filter optional vs required
                        // Filter optional vs required
                        if ($object->type == 'list' && (strpos($object->list, '|') !== false)) {
                            $object->list = explode('|', $object->list);
                        } elseif ($object->type == 'list') {
                            $uri = $object->list;

                            // Check list cache
                            if (empty($lists[$uri])) {
                                $data = json_decode($this->getDocument($uri));
                                $data_set = array();

                                foreach ($data as $o) {
                                    if (! empty($o->{$object->list_option})) {
                                        $data_set[] = $o->{$object->list_option};
                                    }
                                }

                                $lists[$uri] = $data_set;
                            }

                            $object->list = $lists[$uri];
                        }

                        $parameters_optional[$parameter] = $object;
                    }
                }

            }

            // Filter on unnecessary optional parameters
            unset($parameters_optional['cache_minutes']);
            unset($parameters_optional['draft']);
            unset($parameters_optional['draft_flag']);
            unset($parameters_optional['username']);
            unset($parameters_optional['user_id']);
            unset($parameters_optional['job_id']);

            // Get dataset updates information
            $updates_info = \DB::table('definitions_updates')
            ->where('definition_id', $id)
            ->select('username','updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

            return \View::make('ui.datasets.edit')
                        ->with('title', 'Edit a dataset | The Datatank')
                        ->with('definition', $definition)
                        ->with('mediatype', $mediatype)
                        ->with('parameters_required', $parameters_required)
                        ->with('parameters_optional', $parameters_optional)
                        ->with('parameters_dc', $parameters_dc)
                        ->with('parameters_geodcat', $parameters_geodcat)
                        ->with('source_definition', $source_definition)
                        ->with('updates_info', $updates_info);

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
        //\App::abort(400, "Deleting dataset.");

        // Set permission
        Auth::requirePermissions('admin.dataset.delete');

        if (is_numeric($id)) {
            $definition = \Definition::find($id);
            if ($definition) {
                // Delete definition updates
                \DB::table('definitions_updates')->where('definition_id', $id)->delete();

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

    /**
     * Translate the view properties of the parameters
     *
     * @param array  $parameters
     * @param string $media_type
     *
     * @return array
     */
    private function translateParameters($parameters, $media_type)
    {
        $translatedParameters = [];

        foreach ($parameters as $name => $param) {
            $trans_param_name = $media_type . '_' . $name;
            $trans_param_desc_name = $trans_param_name . '_desc';

            $param->name = trans('parameters.' . $trans_param_name);
            $param->description = trans('parameters.' . $trans_param_desc_name);
            $translatedParameters[$name] = $param;
        }

        return $translatedParameters;
    }
	
    /**
     * Autocomplete endpoint "Linking Datasets"
     *
     * @return json
     */	
	public function autocompleteLinkedDatasets(){
		$term = \Input::get('term');
				
		$results = array();
		
		$queries = \DB::table('definitions')
			->where('title', 'LIKE', '%' . $term . '%')
            ->orWhere('description', 'LIKE', '%' . $term . '%')
            ->orWhere('resource_name', 'LIKE', '%' . $term . '%')
            ->orWhere('collection_uri', 'LIKE', '%' . $term . '%')
			->get();
		
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'value' => $query->title ];
		}
		
		return \Response::json($results);
	}		
	
}

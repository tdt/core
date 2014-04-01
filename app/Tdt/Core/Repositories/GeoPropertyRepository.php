<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\GeoPropertyRepositoryInterface;

class GeoPropertyRepository extends BaseDefinitionRepository implements GeoPropertyRepositoryInterface
{

    public static $geotypes = array('polygon', 'latitude', 'longitude', 'polyline', 'multiline', 'point');

    public function __construct(\GeoProperty $model)
    {
        $this->model = $model;
    }

    protected $rules = array(
        'property' => 'required',
        'path' => 'required',
    );

    public function store(array $input)
    {
        return \GeoProperty::create($input);
    }

    public function getGeoProperties($property_id, $type)
    {
        return \GeoProperty::where('source_id', '=', $property_id)->where('source_type', '=', $type, 'AND')->get()->toArray();
    }

    public function validateBulk(array $extracted_geo, array $provided_geo)
    {
        // We don't have any extracted geo properties
        // If the provided ones qualify, validation is ok
        if (empty($extracted_geo)) {

            $this->validate($provided_geo);

            return $provided_geo;
        }

        // If we have extracted geo properties
        // The provided ones will have to be the same
        // as the extracted ones to pass validation
        if (!empty($extracted_geo)) {

            if (!empty($provided_geo) && $extracted_geo != $provided_geo) {
                \App::abort(400, "The geo properties provided didn't match the geo properties that were extracted from the source.");
            }

            $this->validate($extracted_geo);

            return $extracted_geo;
        }
    }

    public function validate(array $input)
    {
        foreach ($input as $geo) {

            // Validate the parameters to their rules
            $validator = $this->getValidator($geo);

            // If any validation fails, return a message and abort the workflow
            if ($validator->fails()) {

                $messages = $validator->messages();
                \App::abort(400, $messages->first());
            }

            // Checkc if the given type is valid
            $type = mb_strtolower($geo['property']);

            if (!in_array($type, self::$geotypes)) {
                $types = implode(', ', self::$geotypes);
                \App::abort(400, "The given geo type ($type) is not supported, the supported list is: $types.");
            }
        }
    }

    public function storeBulk($property_id, $type, array $input)
    {
        foreach ($input as $geo) {
            $geo['source_id'] = $property_id;
            $geo['source_type'] = $type;
            $this->store($geo);
        }
    }

    public function deleteBulk($property_id, $type)
    {
        $geo_properties = \GeoProperty::where('source_id', '=', $property_id)->where('source_type', '=', $type, 'AND')->get();

        foreach ($geo_properties as $geo_property) {
            $geo_property->delete();
        }
    }

    /**
     * Retrieve the set of create parameters that make up a TabularColumn model.
     */
    public function getCreateParameters()
    {
        $geo_type_string = implode(',', self::$geotypes);

        return array(
            'property' => array(
                'required' => false,
                'name' => 'Property',
                'description' => "This must be a string holding one of the following values $geo_type_string.",
                'type' => 'string',
            ),
            'path' => array(
                'required' => false,
                'name' => 'Path',
                'description' => 'This takes on the path to the value of the property, for tabular data for example this will be the alias of the column that holds the property value.',
                'type' => 'string',
            ),
        );
    }
}

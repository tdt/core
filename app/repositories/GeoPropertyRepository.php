<?php

namespace repositories;

use repositories\interfaces\GeoPropertyRepositoryInterface;

class GeoPropertyRepository extends BaseRepository implements GeoPropertyRepositoryInterface
{

    public static $geotypes = array('polygon', 'latitude', 'longitude', 'polyline', 'multiline', 'point');

    public function __construct(\GeoProperty $model){
        $this->model = $model;
    }

    protected $rules = array(
        'property' => 'required',
        'path' => 'required',
    );

    public function store($input){

        return \GeoProperty::create($input);
    }

    public function getGeoProperties($id, $type){

        return \GeoProperty::where('source_id', '=', $id)->where('source_type', '=', $type, 'AND')->get()->toArray();
    }

    public function validate($input){

        foreach($input as $geo){

            // Validate the parameters to their rules
            $validator = $this->getValidator($geo);

            // If any validation fails, return a message and abort the workflow
            if($validator->fails()){

                $messages = $validator->messages();
                \App::abort(400, $messages->first());
            }

            // Checkc if the given type is valid
            $type = mb_strtolower($geo['property']);
            if(!in_array($type, self::$geotypes)){

                $types = implode(', ', self::$geotypes);
                \App::abort(400, "The given geo type ($type) is not supported, the supported list is: $types.");
            }
        }
    }

    public function storeBulk($id, $type, $input){

        foreach($input as $geo){
            $geo['source_id'] = $id;
            $geo['source_type'] = $type;
            $this->store($geo);
        }
    }

    public function deleteBulk($id, $type){

        $geo_properties = \GeoProperty::where('source_id', '=', $id)->where('source_type', '=', $type, 'AND')->get();

        foreach($geo_properties as $geo_property){
            $geo_property->delete();
        }
    }

    /**
     * Retrieve the set of create parameters that make up a TabularColumn model.
     */
    public function getCreateParameters(){

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
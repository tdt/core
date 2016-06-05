<?php

/**
 * Location model.
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Location extends Eloquent
{
    protected $table = 'locations';

    public function definition()
    {
        return $this->belongsTo('Definition');
    }

    /**
     * A location has many geometries
     */
    public function geometry()
    {
        return $this->hasOne('Geometry', 'location_id');
    }

    /**
     * A location has many labels
     */
    public function label()
    {
        return $this->hasOne('Label');
    }
}

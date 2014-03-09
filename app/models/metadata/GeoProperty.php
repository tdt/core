<?php

/**
 * Geo properties model.
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class GeoProperty extends Eloquent
{

    protected $table = 'geoproperties';

    protected $fillable = array('path', 'property', 'source_id', 'source_type');

    /**
     * Return the polymorphic relation with a source type.
     */
    public function source()
    {
        return $this->morphTo();
    }
}

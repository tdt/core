<?php

/**
 * Definition model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Definition extends Eloquent
{

    protected $fillable = array('title','description','date','type','format','source','language','rights', 'cache_minutes', 'draft', 'map_property');

    /**
     * Return the poly morphic relationship with a source type.
     */
    public function source()
    {
        return $this->morphTo();
    }

    /**
     * Delete the related source type
     */
    public function delete()
    {

        $source_type = $this->source()->first();
        $source_type->delete();

        parent::delete();
    }

    /**
     * Draft is a tinyint, cast type true/false to
     * the corrersponding integers in the back-end
     */
    public function setDraftAttribute($value)
    {
        $this->attributes['draft'] = (int) $value;
    }
}

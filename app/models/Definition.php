<?php

/**
 * Definition model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Definition extends Eloquent
{
    protected $fillable = array(
        'title',
        'description',
        'type',
        'language',
        'rights',
        'cache_minutes',
        'keywords',
        'publisher_uri',
        'publisher_name',
        'theme',
        'date',
        'contact_point',
    );

    /**
     * Return the poly morphic relationship with a source type.
     */
    public function source()
    {
        return $this->morphTo();
    }

    public function location()
    {
        return $this->hasOne('Location', 'definition_id');
    }

    public function attributions()
    {
        return $this->hasMany('Attribution', 'definition_id');
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

    public function facets()
    {
        return $this->hasMany('Facet');
    }
}

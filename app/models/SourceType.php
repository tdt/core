<?php

/**
 * Base model for every publishable source (CSV, SHP, ...).
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class SourceType extends Eloquent
{

    protected $appends = array('type', 'cache');

    /**
     * Relationship with the Definition model.
     */
    public function definition()
    {
        return $this->morphOne('Definition', 'source');
    }

    public function getTypeAttribute()
    {
        return str_replace('DEFINITION', '', strtoupper(get_called_class()));
    }

    public function getCacheAttribute()
    {

        if (!empty($this->definition)) {
            return (is_null($this->definition->cache_minutes))? 1 : $this->definition->cache_minutes;
        }

        return 1;
    }
}

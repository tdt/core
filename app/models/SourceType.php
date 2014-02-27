<?php

/**
 * Base model for every publishable source (CSV, SHP, ...).
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class SourceType extends Eloquent{

    protected $appends = array('type');

    /**
     * Relationship with the Definition model.
     */
    public function definition(){
        return $this->morphOne('Definition', 'source');
    }

    public function getTypeAttribute(){
        return str_replace('DEFINITION', '', strtoupper(get_called_class()));
    }

    /**
     * Get cache expiration time
     */
    public function getCacheExpiration(){
        return (is_null($this->definition->cache_minutes))? 1 : $this->definition->cache_minutes;
    }
}

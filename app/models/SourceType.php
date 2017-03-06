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
    /*protected $appends = array('type', 'cache', 'jobid', 'username', 'userid');*/

    /**
     * Relationship with the Definition model.
     */
    public function definition()
    {
        return $this->morphOne('Definition', 'source');
    }
	
	/*
    public function getJobidAttribute()
    {
        if (!empty($this->definition)) {
            return $this->definition->job_id;
        }

        return null;		      
    }
	
    public function getUsernameAttribute()
    {
        if (!empty($this->definition)) {
            return $this->definition->username;
        }
		
		$user = \Sentry::getUser();	

        return $user->email;
    }	
	
    public function getUseridAttribute()
    {
        if (!empty($this->definition)) {
            return $this->definition->user_id;
        }
		
		$user = \Sentry::getUser();		

        return $user->id;
    }
	*/

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

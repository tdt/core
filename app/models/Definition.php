<?php

/**
 * Definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Definition extends Eloquent{

    protected $guarded = array('id', 'source_id');

    /**
     * Return the poly morphic relationship with a source type.
     */
    public function source(){
        return $this->morphTo();
    }
}
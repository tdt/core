<?php

/**
 * FacetType settings model
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class FacetType extends Eloquent
{

    protected $table = 'definition_facet_types';

    protected $fillable = ['facet_name'];

    public $timestamps = false;
}

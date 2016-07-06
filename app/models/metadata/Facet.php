<?php

/**
 * Facet settings model
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Facet extends Eloquent
{

    protected $table = 'definition_facets';

    protected $fillable = ['definition_id', 'facet_id', 'facet_name', 'value'];

    public $timestamps = false;
}

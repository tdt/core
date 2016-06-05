<?php

/**
 * Geometry model.
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Geometry extends Eloquent
{
    protected $table = 'geometries';

    protected $fillable = ['type', 'geometry'];
}

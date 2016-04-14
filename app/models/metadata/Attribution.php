<?php

/**
 * Attribution model.
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Attribution extends Eloquent
{
    protected $table = 'attributions';

    protected $fillable = ['email', 'name', 'role'];
}

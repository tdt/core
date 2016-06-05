<?php

/**
 * Label model.
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Label extends Eloquent
{
    protected $table = 'labels';

    protected $fillable = ['label'];
}

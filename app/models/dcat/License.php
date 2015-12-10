<?php

/**
 * License model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class License extends Eloquent
{
    protected $boolean_values = array();

    protected $fillable = array(
                            'license_id',
                            'title',
                            'url'
                        );
}

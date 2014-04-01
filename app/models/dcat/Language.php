<?php

/**
 * Language model
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Language extends Eloquent
{
    protected $fillable = array('lang_id', 'lang_code', 'name');
}

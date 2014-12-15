<?php

/**
 * General settings model
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Setting extends Eloquent
{

    protected $table = 'general_settings';

    protected $primaryKey = 'key';

    protected $fillable = array('key', 'value');
}

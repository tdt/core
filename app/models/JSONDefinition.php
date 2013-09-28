<?php

/**
 * JSON definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class JSONDefinition extends Eloquent{

    protected $table = 'jsondefinitions';

    protected $guarded = array('id');
}
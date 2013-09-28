<?php

/**
 * Installed definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class InstalledDefinition extends Eloquent{

    protected $table = 'installeddefinitions';

	protected $guarded = array('id');
}
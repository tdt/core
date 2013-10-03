<?php

/**
 * XMl definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class XMLDefinition extends Eloquent{

    protected $table = 'xmldefinitions';

    protected $guarded = array('id');
}
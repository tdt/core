<?php

/**
 * JSONLD definition model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class JsonldDefinition extends SourceType
{

    protected $table = 'jsonlddefinitions';

    protected $fillable = array('uri', 'description');

}

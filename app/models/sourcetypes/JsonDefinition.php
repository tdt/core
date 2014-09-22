<?php

/**
 * JSON definition model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class JsonDefinition extends SourceType
{

    protected $table = 'jsondefinitions';

    protected $fillable = array('uri', 'description', 'title');

}

<?php

/**
 * RML definition model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class RmlDefinition extends SourceType
{

    protected $table = 'rmldefinitions';

    protected $fillable = array('mapping_document');

}

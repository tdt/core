<?php

/**
 * Installed definition model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class InstalledDefinition extends SourceType
{

    protected $table = 'installeddefinitions';

    protected $fillable = array('path', 'description', 'class', 'title');
}

<?php

/**
 * XMl definition model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class XmlDefinition extends SourceType
{

    protected $table = 'xmldefinitions';

    protected $fillable = array('uri', 'description');
}

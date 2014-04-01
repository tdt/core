<?php

/**
 * SPARQL definition model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class SparqlDefinition extends SourceType
{

    protected $table = 'sparqldefinitions';

    protected $fillable = array('endpoint', 'query', 'endpoint_user', 'endpoint_password', 'description');
}

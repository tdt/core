<?php

/**
 * Elasticsearch definition model
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class ElasticsearchDefinition extends SourceType
{
    protected $table = 'elasticsearchdefinitions';

    protected $fillable = array('host', 'es_type', 'es_index', 'port', 'username', 'password', 'description', 'title');
}

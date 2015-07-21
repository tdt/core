<?php

/**
 * Mongo definition model
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class MongoDefinition extends SourceType
{
    protected $table = 'mongodefinitions';

    protected $fillable = array('host', 'mongo_collection', 'database', 'port', 'username', 'password', 'description', 'title');
}

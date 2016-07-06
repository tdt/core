<?php

/**
 * Remote definition model
 * a model representing a dataset that is not locally hosted or managed, but harvested
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class RemoteDefinition extends SourceType
{
    protected $table = 'remotedefinitions';

    protected $fillable = ['dcat'];
}

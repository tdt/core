<?php

/**
 * Inspire definition model
 * a model representing a dataset that is not locally hosted or managed, but harvested
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class InspireDefinition extends SourceType
{
    protected $table = 'inspiredefinitions';

    protected $fillable = ['original_document'];
}

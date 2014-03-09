<?php

/**
 * Ontology model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Ontology extends Eloquent
{

    protected $table = 'ontologies';

    protected $fillable = array(
                            'prefix',
                            'uri',
                        );
}
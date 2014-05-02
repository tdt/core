<?php

/**
 * Rdf definition model
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class RdfDefinition extends SourceType
{
    protected $table = 'rdfdefinitions';

    protected $fillable = array('uri', 'description', 'format');

    /**
     * Relationship with the Definition model
     */
    public function definition()
    {
        return $this->morphOne('Definition', 'source');
    }
}

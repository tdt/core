<?php

/**
 * Geoprojection model.
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class Geoprojection extends Eloquent
{

    protected $table = 'geoprojections';

    protected $fillable = array('epsg', 'projection');

    /**
     * Return the polymorphic relation with a source type.
     */
    public function source()
    {
        return $this->morphTo();
    }
}

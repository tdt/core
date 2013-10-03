<?php

/**
 * Excell definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class XLSDefinition extends Eloquent{

    protected $table = 'xlsdefinitions';

    protected $guarded = array('id');

    public function tabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }
}
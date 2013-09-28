<?php

/**
 * Tabular columns model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class TabularColumns extends Eloquent{

    protected $table = 'tabularcolumns';

    protected $guarded = array('*');

    public function tabular(){
        return $this->morphTo();
    }
}
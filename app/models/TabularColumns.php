<?php

class TabularColumns extends Eloquent{

    protected $table = 'tabularcolumns';

    protected $guarded = array('*');

    public function tabular(){
        return $this->morphTo();
    }

}
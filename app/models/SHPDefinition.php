<?php

class SHPDefinition extends Eloquent{

    protected $table = 'shpdefinitions';

    protected $guarded = array('id');

    public function tabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }
}
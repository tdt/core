<?php

class CSVDefinition extends Eloquent{

    protected $table = 'csvdefinitions';

    protected $guarded = array('id');

    public function tabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }
}
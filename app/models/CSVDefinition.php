<?php

class CSVDefinition extends Eloquent{
    protected $table = 'csvdefinitions';

    protected $guarded = array('id', 'source_id');

    public function TabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }
}
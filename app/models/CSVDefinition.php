<?php

class CSVDefinition extends Eloquent{
    protected $guarded = array('id', 'source_id');

    public function TabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }
}
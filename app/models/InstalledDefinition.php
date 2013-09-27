<?php

class InstalledDefinition extends Eloquent{

    protected $table = 'installeddefinition';

	protected $guarded = array('id', 'source_id');
}
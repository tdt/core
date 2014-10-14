<?php

class Theme extends Eloquent
{

    protected $table = 'themes';

    protected $fillable = ['uri', 'label'];
}

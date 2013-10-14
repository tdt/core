<?php

/**
 * Excell definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class XlsDefinition extends Eloquent{

    protected $table = 'xlsdefinitions';

    protected $guarded = array('id');

    public function tabularColumns(){
        return $this->morphMany('TabularColumns', 'tabular');
    }

    /**
     * Retrieve the set of create parameters that make up a XLS definition.
     */
    public static function getCreateParameters(){
        return array(
                'uri' => array(
                    'required' => true,
                    'description' => 'The location of the XS file, either a URL or a local file location.',
                ),
                'sheet' => array(
                    'required' => false,
                    'description' => 'The delimiter of the separated value file.',
                    'default_value' => ',',
                ),
                'has_header_row' => array(
                    'required' => false,
                    'description' => 'Boolean parameter defining if the separated value file contains a header row that contains the column names.',
                    'default_value' => 1,
                ),
                'start_row' => array(
                    'required' => false,
                    'description' => 'Defines the row at which the data (and header row if present) starts in the file.',
                ),
        );
    }

    /**
     * Retrieve the set of validation rules for every create parameter.
     * If the parameters doesn't have any rules, it's not mentioned in the array.
     */
    public static function getCreateValidators(){
        return array(
            'has_header_row' => 'integer|min:0|max:1',
            'start_row' => 'integer',
        );
    }
}
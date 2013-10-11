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

    /**
     * Retrieve the set of create parameters that make up a XLS definition.
     */
    public static function getCreateParameters(){
        return array(
                array(
                    'name' => 'uri',
                    'required' => true,
                    'description' => 'The location of the XS file, either a URL or a local file location.',
                ),
                array(
                    'name' => 'sheet',
                    'required' => false,
                    'description' => 'The delimiter of the separated value file.',
                    'default_value' => ',',
                ),
                array(
                    'name' => 'has_header_row',
                    'required' => false,
                    'description' => 'Boolean parameter defining if the separated value file contains a header row that contains the column names.',
                    'default_value' => 1,
                    'rules' => 'integer|min:0|max:1'
                ),
                array(
                    'name' => 'pk',
                    'required' => false,
                    'description' => 'Name of the column that will be used as a primary key in the results when retrieving the data.',
                ),
                array(
                    'name' => 'start_row',
                    'required' => false,
                    'description' => 'Defines the row at which the data (and header row if present) starts in the file.',
                    'rules' => 'integer',
                ),
        );
    }
}
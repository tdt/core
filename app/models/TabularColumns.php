<?php

/**
 * Tabular columns model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class TabularColumns extends Eloquent{

    protected $table = 'tabularcolumns';

    protected $guarded = array('id', 'source_id', 'source_type');

    public function tabular(){
        return $this->morphTo();
    }

    /**
     * Retrieve the set of create parameters that make up a TabularColumn model.
     */
    public static function getCreateProperties(){

        return array(
            'columns' => array(
                'required' => false,
                'description' => 'Columns should be an array of columns indicis mapped onto the column name. This given column name will replace the original column name that is retrieved from the file itself.',
            ),
            'pk' => array(
                'required' => false,
                'description' => 'The index of the column that serves as a primary key when data is published. Rows will thus be indexed onto the value of the column which index is represented by the pk value.',
            ),
        );
    }

    /**
     * Return the set of rules for the parameters for validation purposes.
     */
    public static function getCreateValidators(){
        return array(
            'pk' => 'integer',
        );
    }

    /**
     * Validate the parameters.
     */
    public static function validate($params){

        $validated_params = array();

        $create_params = self::getCreateProperties();
        $rules = self::getCreateValidators();

        array_keys($create_params);

        // Validate the parameters to their rules.
        $validator = Validator::make(
                        $params,
                        $rules
                    );

        // If any validation fails, return a message and abort the workflow.
        if($validator->fails()){

            $messages = $validator->messages();
            \App::abort(452, $messages->first());
        }
    }
}

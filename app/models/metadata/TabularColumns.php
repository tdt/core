<?php

/**
 * Tabular columns model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class TabularColumns extends Eloquent{

    protected $table = 'tabularcolumns';

    protected $fillable = array('index', 'column_name', 'is_pk', 'column_name_alias');

    public function tabular(){
        return $this->morphTo();
    }

    /**
     * Retrieve the set of create parameters that make up a TabularColumn model.
     */
    public static function getCreateParameters(){

        return array(
            'column_name' => array(
                'required' => false,
                'description' => 'The column name that corresponds with the index.',
            ),
            'pk' => array(
                'required' => false,
                'description' => 'The index of the column that serves as a primary key when data is published. Rows will thus be indexed onto the value of the column which index is represented by the pk value.',
            ),
            'index' => array(
                'required' => false,
                'description' => 'The index of the column starting from 0.',
            ),
            'column_name_alias' => array(
                'required' => false,
                'description' => 'Provides an alias for the column name and will be used when data is requested instead of the column_name property.',
            ),
        );
    }

    /**
     * Return the set of rules for the parameters for validation purposes.
     */
    public static function getCreateValidators(){
        return array(
            'pk' => 'integer',
            'index' => 'required',
            'column_name' => 'required',
            'column_name_alias' => 'required',
        );
    }

    /**
     * Validate the parameters.
     */
    public static function validate($params){

        // If no columns are passed, then we'll parse them ourselves
        if(empty($params)){
            return;
        }

        $validated_params = array();

        $create_params = self::getCreateParameters();
        $rules = self::getCreateValidators();

        //array_keys($create_params);

        foreach($params as $column_entry){

            // Validate the parameters to their rules
            $validator = Validator::make(
                            $column_entry,
                            $rules
                        );

            // If any validation fails, return a message and abort the workflow
            if($validator->fails()){

                $messages = $validator->messages();
                \App::abort(452, $messages->first());
            }
        }
    }
}

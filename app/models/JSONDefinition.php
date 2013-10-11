<?php

/**
 * JSON definition model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class JSONDefinition extends Eloquent{

    protected $table = 'jsondefinitions';

    protected $guarded = array('id');

    /**
     * Retrieve the set of create parameters that make up a JSON definition.
     */
    public static function getCreateParameters(){
        return array(
            array(
                'name' => 'uri',
                'required' => true,
                'description' => 'The location of the JSON file, this should either be a URL or a local file location.',
            ),
        );
    }
}
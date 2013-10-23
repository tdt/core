<?php

/**
 * Dublic core model.
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DublinCore extends Eloquent{

    protected $table = 'dublincore';

    protected $guarded = array('*');

    /**
     * Provide a morphological relationship with this model.
     */
    public function metadata(){
        return $this->morphTo();
    }

    /**
     * Retrieve the set of create parameters that make up a TabularColumn model.
     */
    public static function getCreateProperties(){

        return array(
            'title' => array(
                'required' => false,
                'description' => 'A name given to the resource.',
            ),
            'creator' => array(
                'required' => false,
                'description' => 'An entity primarily responsible for making the resource.',
            ),
            'subject' => array(
                'required' => false,
                'description' => 'The topic of the resource.'
            ),
            'description' => array(
                'required' => false,
                'description' => 'An account of the resource.'
            ),
            'publisher' => array(
                'required' => false,
                'description' => 'An entity responsible for making the resource available.'
            ),
            'contributor' => array(
                'required' => false,
                'description' => 'An entity responsible for making contributions to the
                resource.',
            ),
            'date' => array(
                'required' => false,
                'description' => 'A point or period of time associated with an event
                in the lifecycle of the resource.'
            ),
            'type' => array(
                'required' => false,
                'description' => 'The nature or genre of the resource.'
            ),
            'format' => array(
                'required' => false,
                'description' => 'The file format, physical medium, or dimensions
                of the resource.'
            ),
            'identifier' => array(
                'required' => false,
                'description' => 'An unambiguous reference to the resource within a given
                context.'
            ),
            'source' => array(
                'required' => false,
                'description' => 'A related resource from which the described resource
                is derived.'
            ),
            'language' => array(
                'required' => false,
                'description' => 'A language of the resource.'
            ),
            'relation' => array(
                'required' => false,
                'description' => 'A related resource.'
            ),
            'coverage' => array(
                'required' => false,
                'description' => 'The spatial or temporal topic of the resource, the
                spatial applicability of the resource, or the
                jurisdiction under which the resource is relevant.'
            ),
            'rights' => array(
                'required' => false,
                'description' => 'Information about rights held in and over the resource.'
            ),
        );
    }
}
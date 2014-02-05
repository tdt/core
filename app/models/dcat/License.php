<?php

/**
 * License model
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class License extends Eloquent{

    protected $boolean_values = array('domain_content', 'domain_data', 'is_generic', 'is_okd_compliant', 'is_osi_compliant');

    protected $fillable = array(
                            'domain_content',
                            'domain_data',
                            'domain_software',
                            'family',
                            'license_id',
                            'is_generic',
                            'is_okd_compliant',
                            'is_osi_compliant',
                            'maintainer',
                            'status',
                            'title',
                            'url'
                        );

     public static function getColumns(){
        return array(
            'domain_content',
            'domain_data',
            'domain_software',
            'license_id',
            'is_generic',
            'is_okd_compliant',
            'is_osi_compliant',
            'maintainer',
            'status',
            'title',
            'url'
        );
    }

    /**
     * Since MySQL doesn't have the notion of boolean values true and false
     * provide a translation for these 0 and 1 values to PHP booleans
     *
     * @return mixed
     */
    public function __get($name){

        if(in_array($name, $this->boolean_values)){
            return parent::__get($name) == 1 ? TRUE : FALSE;
        }

        return parent::__get($name);
    }
}
<?php

namespace Tdt\Core\Validators;

/**
 * A custom validator that provides extra functions
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class CustomValidator extends \Illuminate\Validation\Validator
{

    /**
     * Check if the given uri can be resolved by using file_get_contents().
     */
    public function validateUri($attribute, $value, $parameters)
    {

        try {

            file_get_contents($value);
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Check if the given value is a proper file that can be opened with fopen().
     */
    public function validateFile($attribute, $value, $parameters)
    {

        try {

            $handle = fopen($value, 'r');
            return $handle;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Check if the given file contains a JSON body.
     */
    public function validateJson($attribute, $value, $parameters)
    {

        try {

            $data = json_decode(file_get_contents($value));

            if (empty($data)) {
                return false;
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Check if the given file is located in the installed folder and the
     */
    public function validateInstalled($attribute, $value, $parameters)
    {
        try {

            $class_file = app_path() . '/../installed/' .  $value;

            return file_exists($class_file);

        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Check if the SPARQL query is legitimate
     */
    public function validateSparqlquery($attribute, $value, $parameters)
    {
        if (stripos($value, 'construct') === false && stripos($value, 'select') === false) {
            return false;
        }

        return true;
    }

    /**
     * Check if the MySQL query is legitimate
     */
    public function validateMysqlquery($attribute, $value, $parameters)
    {
        if (stripos($value, 'select') === false || stripos($value, 'from') === false) {
            return false;
        }

        return true;
    }

    /**
     * Check if the collection uri doesn't contain preserved namespaces
     */
    public function validateCollectionuri($attribute, $value, $parameters)
    {
        $preserved_ns = array('discovery', 'api');

        $collection_uri = explode('/', $value);

        if (in_array(strtolower($collection_uri[0]), $preserved_ns)) {
            return false;
        }

        return true;
    }
}

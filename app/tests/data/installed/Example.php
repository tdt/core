<?php

/**
 * Installed resoruce for stock information using Yahoo API with YQL
 */

class Example
{

    /**
     * The set of REST parameters that this resource requires.
     * Note that
     *      required parameters are passed as part of the URI, not as a query string parameter.
     *      you have access to all of the standard Laravel 4 classes and components (e.g. Request,...)
     *
     */
    public static function getParameters()
    {
        return array();
    }

    /**
     * Set parameters to be used in the read function, you can manipulate or validate your REST parameters here
     */
    public function setParameter($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * For semantic resources only (optional)
     */
    public function getNamespaces()
    {
        return array();
    }

    /**
     * Return an array with your data
     */
    public function getData()
    {
        return array("data" => array("data_property1" => "Not a number"));
    }

}

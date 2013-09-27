<?php
/**
 * The CSV formatter.
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 */

namespace tdt\core\formatters;

interface IFormatter{

    public static function createResponse($dataObj);

    public static function getBody($dataObj);

    public static function getDocumentation(){

}
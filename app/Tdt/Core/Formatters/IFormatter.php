<?php

namespace Tdt\Core\Formatters;

/**
 * Formatter interface
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
interface IFormatter
{

    public static function createResponse($dataObj);

    public static function getBody($dataObj);

    public static function getDocumentation();
}

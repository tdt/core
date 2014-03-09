<?php

/**
 * Use PHP cookies to avoid cookies everywhere else
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace tdt\core\ui\helpers;

class Flash extends \Controller
{

    private static $COOKIE_NAME = 'tdt_admin';

    /**
     * Set a new cookie
     */
    public static function set($string)
    {
        setcookie(self::$COOKIE_NAME, base64_encode($string), time() + 1);
    }

    /**
     * Get a cookie and flush it
     */
    public static function get()
    {
        $cookie = '';

        if (!empty($_COOKIE[self::$COOKIE_NAME])) {
            $cookie = base64_decode($_COOKIE[self::$COOKIE_NAME]);
        }

        self::flush();

        return $cookie;
    }

    /**
     * Flush the cookie
     */
    public static function flush()
    {
        setcookie(self::$COOKIE_NAME, '', time() - 3600);
    }
}

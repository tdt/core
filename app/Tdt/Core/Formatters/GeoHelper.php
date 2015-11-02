<?php

namespace Tdt\Core\Formatters;

/**
 * Helper class for formatters for geo related things.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Dieter De Paepe
 */

class GeoHelper
{
    private static $LONGITUDE_NAMES = array('long', 'lon', 'longitude', 'lng');
    private static $LATITUDE_NAMES = array('lat', 'latitude');

    /**
     * Looks for the presence of commonly used names for latitude/longitude in the given array.
     *
     * @param array $data an associative array
     * @return array|null latitude and longitude (both not null) as an array, or null
     */
    public static function findLatLong($array)
    {
        $latkey = false;
        $longkey = false;

        foreach (self::$LONGITUDE_NAMES as $prefix) {
            $longkey = self::keyExists($prefix, $array);

            if ($longkey) {
                break;
            }
        }

        foreach (self::$LATITUDE_NAMES as $prefix) {
            $latkey = self::keyExists($prefix, $array);

            if ($latkey) {
                break;
            }
        }

        if ($latkey && $longkey) {
            return array($latkey, $longkey);
        } else {
            return null;
        }
    }

    /**
     * Case insensitive version of array_key_exists.
     * Returns the matching key on success, else false.
     *
     * @param string $key
     * @param array $array
     * @return string|false
     */
    public static function keyExists($key, $array)
    {
        if (array_key_exists($key, $array)) {
            return $key;
        }

        $key = strtolower($key);

        foreach ($array as $k => $v) {
            if (strtolower($k) == $key) {
                return $k;
            }
        }
        return false;
    }
}

<?php

namespace tdt\core\cache;

/**
 * Cache Wrapper
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */

class Cache extends \Cache
{

    /**
     * Check if cache is enabled and if a value is already stored
     */
    public static function has($key){
        return \Config::get('cache.enabled', true) && parent::has($key);
    }
}
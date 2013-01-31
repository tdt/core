<?php

/**
 * You can create any configuration parameter in /custom/config.ini
 * @copyright (C) 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert <pieter.colpaert@ugent.be>
 * @author Jan Vansteenlandt <jan@irail.be>
 */

namespace tdt\core\utility;

class Config {

    private static $config;

    /**
     * This function sets the config for the entire framework
     * @param config is a configuration array defined in the README.md
     */
    public static function setConfig(array $config) {
        self::$config = $config;
    }

    public static function get($category, $key = "", $key2 = "") {
        if (self::$config === null) {
            echo "Please set the config using tdt\core\utility\Config::setConfig(\$array);";
            exit();
        }

        // return the right variable according to the config, at max 3 levels deep
        if (isset(self::$config[$category]) && $key === "") {
            return self::$config[$category];
        } else if (isset(self::$config[$category][$key]) && $key2 === "") {
            return self::$config[$category][$key];
        } else if (isset(self::$config[$category][$key][$key2])) {
            return self::$config[$category][$key][$key2];
        } else {
            return "";
        }
    }
    
     /*
     * Get the configuration array
     */
    public static function getConfigArray(){
        return self::$config;
    }

}
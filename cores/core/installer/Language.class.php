<?php
/**
 * Language class and function for installer translations
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 */

class Language {
    
    private $lang = array();
    private $is_loaded = array();
    
    public function load($language) {
        $langfile = dirname(__FILE__)."/lang/".strtolower(str_replace('.php', '', $language)).".php";
        
        if (in_array($langfile, $this->is_loaded, TRUE))
			return;
        
        if(file_exists($langfile)) {
            $lang = array();
            include($langfile);
            $this->lang = array_merge($this->lang, $lang);
            unset($lang);
        }
        
        $this->is_loaded[] = $langfile;
    }
    
    public function lang($key) {
        if(array_key_exists($key, $this->lang))
            return $this->lang[$key];
        return $key;
    }
    
    public static function getInstance($language = "english") {
        static $instance;
        
        if (!isset($instance)) {
            $instance = new Language($language);
        }

        return $instance;
    }
}

function lang($key) {
    $lang = Language::getInstance();
    return $lang->lang($key);
}
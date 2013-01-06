<?php

/**
 * Base installer class that will load the correct controller
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 */
include_once("../../../includes/rb.php");
include_once("../../../framework/Config.class.php");

class Installer {

    private $steps = array("Welcome", "ConfigCheck", "SystemCheck", "DatabaseCheck", "DatabaseCreate", "DatabaseSetup", "Finish");
    // installed languages for this installer
    private $languages = array("en");
    protected $session, $config;
    protected $currentStep;
    protected $nextStep = null;
    protected $previousStep = null;

    public function __construct() {        
        session_start();
        $this->session = &$_SESSION;

        // load language
        $language = Language::getInstance();
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (!in_array($lang, $this->languages))
            $lang = reset($this->languages);

        $language->load($lang);
    }

    public static function version() {
        include(dirname(__FILE__) . "/../version.php");
        return $version;
    }

    public function run() {
        if ($this->installedVersion() == $this->version()) {
            $this->previousStep = FALSE;
            $this->nextStep = FALSE;

            $this->currentStep = end($this->steps);
        } else if (!$this->currentStep)
            $this->currentStep = reset($this->steps);

        $this->loadController($this->currentStep);
    }

    private function loadController($name) {
        $loaded = false;

        $path = dirname(__FILE__) . "/controllers/" . $name . ".class.php";
        if (file_exists($path)) {
            include($path);
            if (class_exists($name)) {
                $controller = new $name();
                $controller->index();
                $loaded = true;
            }
        }

        if (!$loaded) {
            $default = reset($this->steps);
            if ($default != $name)
                $this->loadController($default);
            else
                die("Could not find controller");
        }
    }

    public function advance($next = FALSE) {
        if ($next && in_array($next, $this->steps))
            $this->currentStep = $next;
        elseif (!$next)
            $this->currentStep = $this->nextStep();
    }

    public function nextStep($next = null) {
        // allow override from controllers
        if (!is_null($next))
            $this->nextStep = $next;

        // if no next step is set, detect the next step in line
        if (is_null($this->nextStep)) {
            $next = array_search($this->currentStep, $this->steps) + 1;
            if (array_key_exists($next, $this->steps))
                $this->nextStep = $this->steps[$next];
            else
                $this->nextStep = FALSE;
        }

        return $this->nextStep;
    }

    public function previousStep($previous = null) {
        // allow override from controllers
        if (!is_null($previous))
            $this->previousStep = $previous;

        // if no previous step is set, detect the previous step
        if (is_null($this->previousStep)) {
            $previous = array_search($this->currentStep, $this->steps) - 1;
            if (array_key_exists($previous, $this->steps))
                $this->previousStep = $this->steps[$previous];
            else
                $this->previousStep = FALSE;
        }

        return $this->previousStep;
    }

    public static function installedVersion() {    

        try {                     
            R::setup(Config::get("core", "dbsystem") . ":host=" . Config::get("core", "dbhost") . ";dbname=" . Config::get("core", "dbname"), Config::get("core", "dbuser"), Config::get("core", "dbpassword"));            
            $info = R::getRow("SELECT * FROM info WHERE name = :name LIMIT 0,1", array(":name" => "version"));            

            if (isset($info["value"]))
                return $info["value"];           
        } catch (Exception $e) {           
            return FALSE;
        }

        return FALSE;
    }

    public static function getInstance() {
        static $instance;

        if (!isset($instance)) {
            $instance = new Installer();
        }

        return $instance;
    }

}
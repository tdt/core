<?php
/**
 * Installation step: finish
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 */

class Finish extends InstallController {
    
    public function index() {
        include_once(dirname(__DIR__)."/../../../framework/AutoInclude.class.php");
        $this->installer->previousStep(FALSE);
        $c = Cache::getInstance();
        $c->delete(Config::get("general","hostname") . Config::get("general","subdir") . "documentation");
        $c->delete(Config::get("general","hostname") . Config::get("general","subdir") . "admindocumentation");
        $this->view("finish");
    }
    
}
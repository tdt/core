<?php

/*
 * Class inheriting from the AController from tdt\framework
 * and adding some functionality specifically for Core controllers
 */

namespace tdt\core\controllers;

use tdt\framework\AController;
use tdt\framework\Cache\Cache;
use tdt\framework\Config;
use tdt\framework\includes\GoogleAnalytics\Config;


class ACoreController extends AController {
    /*
     * installation variables
     */

    protected $hostname;
    protected $subdir;

    /*
     * back-end variables
     */
    protected $dbhost;
    protected $dbname;
    protected $dbsystem;
    protected $dbuser;
    protected $dbpassword;

    public function __construct() {
        $this->hostname = Config::get("general", "hostname");
        $this->subdir = Config::get("general", "subdir");

        $this->dbhost = Config::get("db", "host");
        $this->dbname = Config::get("db", "name");
        $this->dbsystem = Config::get("db", "system");
        $this->dbuser = Config::get("db", "user");
        $this->dbpassword = Config::get("db", "password");
    }
    
    protected function initializeDatabaseConnection(){
        R::setup($this->dbsystem . ":host=" . $this->dbhost . ";dbname=" . $this->dbname, $this->dbuser, $this->dbpassword);
    }
    

    protected function clearCachedDocumentation(){
        $c =Cache::getInstance();
        $c->delete($this->hostname . $this->subdir . "documentation");
        $c->delete($this->hostname . $this->subdir . "descriptiondocumentation");
        $c->delete($this->hostname . $this->subdir . "admindocumentation");
        $c->delete($this->hostname . $this->subdir . "packagedocumentation");
    }
    
}

?>

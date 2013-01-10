<?php

/*
 * Class inheriting from the AController from tdt\framework
 * and adding some functionality specifically for Core controllers
 */

namespace tdt\core\controllers;

use tdt\framework\AController;

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
        $this->hostname = tdt\framework\Config::get("general", "hostname");
        $this->subdir = tdt\framework\Config::get("general", "subdir");

        $this->dbhost = tdt\framework\Config::get("db", "host");
        $this->dbname = tdt\framework\Config::get("db", "name");
        $this->dbsystem = tdt\framework\Config::get("db", "system");
        $this->dbuser = tdt\framework\Config::get("db", "user");
        $this->dbpassword = tdt\framework\Config::get("db", "password");
    }
    
    protected function initializeDatabaseConnection(){
        R::setup($this->dbsystem . ":host=" . $this->dbhost . ";dbname=" . $this->dbname, $this->dbuser, $this->dbpassword);
    }
    

    protected function clearCachedDocumentation(){
        $c = tdt\framework\Cache\Cache::getInstance();
        $c->delete($this->hostname . $this->subdir . "documentation");
        $c->delete($this->hostname . $this->subdir . "descriptiondocumentation");
        $c->delete($this->hostname . $this->subdir . "admindocumentation");
        $c->delete($this->hostname . $this->subdir . "packagedocumentation");
    }
    
}

?>

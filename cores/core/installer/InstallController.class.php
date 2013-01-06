<?php
/**
 * Base class for the install controllers that represent a step of the installation
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 */

include_once("../aspects/caching/Cache.class.php");

class InstallController {
    

    protected $installer;
    
    public function __construct() {
        // enable output buffering
                
        ob_start();
        
        // makes it easy for controllers to access the installer
        $this->installer = Installer::getInstance();
    }
    
    public function index() {
        // extend   
    }
    
    public function view($file, $data=array()) {
        // view folder path
        $file = dirname(__FILE__)."/views/".$file.".php";
        
        if(file_exists($file)) {
            extract($data);
            include($file);
        }
        else
            $this->error("Unable to load view: ".$file);
    }
    
    private function error($message) {
        ob_clean();
        
        $this->installer->nextStep(FALSE);
        
        $this->view("header");
        echo "<p>".$message."<p>";
        $this->view("footer");
        
        ob_end_flush();
        die();
    }
    
}
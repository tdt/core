<?php
/**
 * Installation step: welcome
 *
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jens Segers
 */

class Welcome extends InstallController {
    
    public function index() {
        $this->view("welcome");
    }
    
}
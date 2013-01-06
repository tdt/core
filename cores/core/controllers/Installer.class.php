<?php

/**
 * This is the controller which will handle calls to the default index.page
 * It will redirect to the installer.
 *
 * @package The-Datatank/controllers
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

class Installer extends AController{   

    public function GET($matches) {
        //redirect to the installer
        header("Location: cores/core/installer/index.php");
    }
  
}
?>

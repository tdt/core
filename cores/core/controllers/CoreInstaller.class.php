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
class CoreInstaller extends AController {

    public function GET($matches) {
        //redirect to the installer
        $url = Config::get("general","hostname").Config::get("general","subdir")."cores/core/installer/index.php";
        header("Location: $url");       
    }

}

?>

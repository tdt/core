<?php

/*
 * This is an abstract class for installed resources
 *
 * @package The-Datatank/modelpackages/installed/
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3 
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

namespace tdt\core\model\packages\installed;

use tdt\core\model\resources\read\AReader;

abstract class AInstalledResource extends AReader{
    
    public abstract static function getDoc();
}

?>

<?php

/**
 * The global interpreter model. (used when executing a query)
 *
 * @package The-Datatank/universalfilter/interpreter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\interpreter;

use tdt\core\universalfilter\tablemanager\implementation\UniversalFilterTableManager;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

interface IInterpreterControl {

    /**
     * @return UniversalFilterNodeExecuter
     */
    public function findExecuterFor(UniversalFilterNode $filternode);

    /**
     * @return UniversalFilterTableManager
     */
    public function getTableManager();
}

?>

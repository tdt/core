<?php

/**
 * The global interpreter model. (used when executing a query)
 *
 * @package The-Datatank/universalfilter/interpreter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\spectql\implementation\interpreter;

use tdt\core\spectql\implementation\tablemanager\implementation\UniversalFilterTableManager;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

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
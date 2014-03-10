<?php

/**
 * The global interpreter model. (used when executing a query)
 *
 * @package The-Datatank/universalfilter/interpreter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace Tdt\Core\Spectql\implementation\interpreter;

use Tdt\Core\Spectql\implementation\tablemanager\implementation\UniversalFilterTableManager;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

interface IInterpreterControl
{

    /**
     * @return UniversalFilterNodeExecuter
     */
    public function findExecuterFor(UniversalFilterNode $filternode);

    /**
     * @return UniversalFilterTableManager
     */
    public function getTableManager();
}

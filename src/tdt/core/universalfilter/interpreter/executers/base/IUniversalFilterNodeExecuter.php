<?php

/**
 * Base class of all executers
 *
 * @package The-Datatank/universalfilter/interpreter/executers/base
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\interpreter\executers\base;

use tdt\core\universalfilter\data\UniversalFilterTableContent;
use tdt\core\universalfilter\data\UniversalFilterTableHeader;
use tdt\core\universalfilter\interpreter\Environment;
use tdt\core\universalfilter\interpreter\IInterpreterControl;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

interface IUniversalFilterNodeExecuter {

    /**
     * Initializes this node. It gets the environment of the executer as an argument.
     * @param UniversalFilterNode $filter The corresponding filter
     * @param Environment $topenv The environment given to evaluate this filter. It should NEVER be modified.
     * @param IInterpreterControl $interpreter The interpreter that evaluates this tree.
     * @param bool $preferColumn Does the parent expression would like me to give back a column?
     */
    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn);

    /**
     * Returns the header of the returned table
     *
     * @return UniversalFilterTableHeader
     */
    public function getExpressionHeader();

    /**
     * Calculates and returns the content of the table
     *
     * @return UniversalFilterTableContent
     */
    public function evaluateAsExpression();

    /**
     * Cleanup
     */
    public function cleanUp();

    /**
     * This method modifies the given query and adds the expected columnNames...
     */
    public function modififyFiltersWithHeaderInformation();

    /**
     * Finds out which sources this executer uses,
     * and which parts of the query can be executed on one source
     *
     * @param UniversalFilterNode $filter The parent
     * @param int $parentIndex The index in the parent.
     * @return array of SourceUsageData
     */
    public function filterSingleSourceUsages(UniversalFilterNode $parentNode, $parentIndex);
}

?>

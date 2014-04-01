<?php

/*
 * This class can iterate over a query tree and pick out certain nodes.
 *
 * @package The-Datatank/universalfilter/interpreter/other
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 *
 */

namespace Tdt\Core\Spectql\implementation\interpreter\other;

use Tdt\Core\Spectql\implementation\converter\SQLConverter;
use Tdt\Core\Spectql\implementation\converter\NoSQLConverter;
use Tdt\Core\Spectql\implementation\sourcefilterbinding\ExpectedHeaderNamesAttachment;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

class QueryTreeHandler
{

    private $sqlConverter;
    private $noSqlConverter;
    private $query;

    public function __construct(UniversalFilterNode $query)
    {
        $this->query = $query;
        $requiredColumnNames = $query->getAttachment(ExpectedHeaderNamesAttachment::$ATTACHMENTID);
        $headerNames = $requiredColumnNames->getExpectedHeaderNames();
        $this->sqlConverter = new SQLConverter($headerNames);
        $this->sqlConverter->treeToSQLClauses($query);

        $this->noSqlConverter = new noSqlConverter($headerNames);
        $this->noSqlConverter->treeToSQLClauses($query);

    }

    public function getNoSqlConverter()
    {
        return $this->noSqlConverter;
    }

    public function getSqlConverter()
    {
        return $this->sqlConverter;
    }

    /*
     * Expects a clause name, currently supported orderby, where, groupby,select
     */

    public function getNodeForClause($clause)
    {

        $currentNode = $this->query;
        $parentNode = null;
        $found = false;

        while ($currentNode != null && !$found) {
            $type = $currentNode->getType();

            switch ($clause) {
                case "orderby":
                    if ($type == "FILTERSORTCOLUMNS") {
                        $found = true;
                    }
                    break;
                case "where":
                    if ($type == "FILTEREXPRESSION") {
                        $found = true;
                    }
                    break;
                case "groupby":
                    if ($type == "DATAGROUPER") {
                        $found = true;
                    }
                    break;
                case "select":
                    if ($type == "FILTERCOLUMN") {
                        $found = true;
                    }
                    break;
                case "limit":
                    if ($type == "FILTERLIMIT") {
                        $found = true;
                    }
                    break;
            }

            if (method_exists($currentNode, "getSource")) {
                $parentNode = $currentNode;
                $currentNode = $currentNode->getSource();
            } else {
                $currentNode = null;
            }
        }
        if (!$found) {
            return null;
        }

        return $parentNode;
    }
}

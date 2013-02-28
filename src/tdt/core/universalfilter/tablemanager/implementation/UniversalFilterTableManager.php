<?php

/**
 * This it the implementation of the TableManager for The-DataTank
 *
 * The TableManager makes it easier to view The DataTank as a collection of tables
 *
 * @package The-Datatank/universalfilter/tablemanager
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */

namespace tdt\core\universalfilter\tablemanager\implementation;

use tdt\core\model\ResourcesModel;
use tdt\core\universalfilter\data\UniversalFilterTableHeader;
use tdt\core\universalfilter\data\UniversalFilterTableHeaderColumnInfo;
use tdt\core\universalfilter\sourcefilterbinding\ExternallyCalculatedFilterNode;
use tdt\core\universalfilter\tablemanager\IUniversalFilterTableManager;
use tdt\core\universalfilter\tablemanager\implementation\tools\PhpObjectTableConverter;
use tdt\core\universalfilter\tablemanager\implementation\UniversalFilterTableManager;
use tdt\core\universalfilter\UniversalFilterNode;
use tdt\core\utility\Config;
use tdt\exceptions\TDTException;
use tdt\core\utility\RequestURI;

class UniversalFilterTableManager implements IUniversalFilterTableManager {

    private static $IDENTIFIERSEPARATOR = ".";
    private static $HIERARCHICALDATESEPARATOR = ":";
    private $requestedTableHeaders = array();
    private $requestedTables = array();
    private $resourcesmodel;

    public function __construct() {
        $this->resourcesmodel = ResourcesModel::getInstance(Config::getConfigArray());
    }

    /**
     * Gets the resource as a php object
     * @param type $package
     * @param type $resource
     * @return type phpObject
     */
    private function getFullResourcePhpObject($package, $resource, $RESTparameters = array()) {

        $model = ResourcesModel::getInstance(Config::getConfigArray());

        $doc = $model->getAllDoc();
        $parameters = array();

        foreach ($doc->$package->$resource->requiredparameters as $parameter) {
            //set the parameter of the method

            if (!isset($RESTparameters[0])) {
                $exception_config = array();
                $exception_config["log_dir"] = Config::get("general", "logging", "path");
                $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                throw new TDTException(452, "Invalid parameter given: " . $parameter, $exception_config);
            }
            $parameters[$parameter] = $RESTparameters[0];
            //removes the first element and reindex the array - this way we'll only keep the object specifiers (RESTful filtering) in this array
            array_shift($RESTparameters);
        }
        /**
         * Pass along limit and offset if given
         */
        $ru = RequestURI::getInstance(Config::getConfigArray());
        $pageURL = $ru->getURI();
        $matches = array();

        if(preg_match('/.*\.limit\((.*)\).*/',$pageURL,$matches)){
            $limit_items = explode(",",$matches[1]);
            $limit = 0;
            $offset = 0;

            if(count($limit_items) == 2){
                $offset = array_shift($limit_items);
                $limit = array_shift($limit_items);
            }else{
                $limit = array_shift($limit_items);
            }
        }

        $parameters["limit"] = $limit;
        $parameters["offset"] = $offset;

        /**
         * Read the resource from the model
         */
        $resourceObject = $model->readResource($package, $resource, $parameters, $RESTparameters);

        //implement cache

        return $resourceObject;
    }

    /**
     * Splits the identifier in 3 pieces:
     *  - a package (string)
     *  - a resource (string)
     *  - a array of subidentifiers
     *
     * @param string $globalTableIdentifier => see universal/UniversalFilters.php/Identifier for format
     * @return array of the three pieces
     */
    private function splitIdentifier($globalTableIdentifier) {

        $identifierparts = explode(UniversalFilterTableManager::$HIERARCHICALDATESEPARATOR, $globalTableIdentifier);
        $hierarchicalsubparts = array();
        if (isset($identifierparts[1]) && strlen($identifierparts[1]) > 0) {
            $hierarchicalsubparts = explode(".", $identifierparts[1]);
        }

        $identifierpieces = explode(UniversalFilterTableManager::$IDENTIFIERSEPARATOR, $identifierparts[0]);

        $packageresourcestring = implode("/", $identifierpieces);

        // The function will throw an exception if a package hasn't been found that matches
        // it will not however throw an exception if no resource has been found.
        $result = $this->resourcesmodel->processPackageResourceString($packageresourcestring);

        if ($result["resourcename"] == "") {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("Illegal identifier. Package does not contain a resourcename: "
                . $globalTableIdentifier),$exception_config);
        }

        return array($result["packagename"], $result["resourcename"], $result["RESTparameters"], $hierarchicalsubparts);
    }

    private function loadTable($globalTableIdentifier) {

        $splitedId = $this->splitIdentifier($globalTableIdentifier);

        $converter = new PhpObjectTableConverter();

        $resource = $this->getFullResourcePhpObject($splitedId[0], $splitedId[1], $splitedId[2]);

        $table = $converter->getPhpObjectTable($splitedId, $resource);

        $this->requestedTables[$globalTableIdentifier] = $table;

        $table->getContent()->tableNeeded(); //do not destroy content... it's cached...
    }

    private function loadTableWithHeader($globalTableIdentifier, $header) {

        $splitedId = $this->splitIdentifier($globalTableIdentifier);

        $converter = new PhpObjectTableConverter();

        $resource = $this->getFullResourcePhpObject($splitedId[0], $splitedId[1], $splitedId[2]);

        $table = $converter->getPhpObjectTableWithHeader($splitedId, $resource, $header);

        $this->requestedTables[$globalTableIdentifier] = $table;

        $table->getContent()->tableNeeded(); //do not destroy content... it's cached...
    }

    /**
     * The UniversalInterpreter found a identifier for a table.
     * Can you give me the header of the table?
     *
     * @param string $globalTableIdentifier
     * @return UniversalFilterTableHeader
     */
    public function getTableHeader($globalTableIdentifier) {

        $model = ResourcesModel::getInstance(Config::getConfigArray());
        $identifierpieces = $this->splitIdentifier($globalTableIdentifier);

        $column = NULL;
        try {
            $columns = $model->getColumnsFromResource($identifierpieces[0], $identifierpieces[1]);

        } catch (Exception $e) {
            $columns = NULL;
        }

        if ($columns != NULL && !isset($this->requestedTableHeaders[$globalTableIdentifier])) {
            $headerColumns = array();
            foreach ($columns as $column) {
                $nameParts = array(); //explode(".",$globalTableIdentifier);
                array_push($nameParts, $column["column_name"]);
                $headerColumn = new UniversalFilterTableHeaderColumnInfo($nameParts);
                array_push($headerColumns, $headerColumn);
            }

            $converter = new PhpObjectTableConverter();
            $nameOfTable = $converter->getNameOfTable($identifierpieces);

            $tableHeader = new UniversalFilterTableHeader($headerColumns, false, false);

            $this->requestedTableHeaders[$globalTableIdentifier] = $tableHeader;
            return $tableHeader;
        } elseif (isset($this->requestedTableHeaders[$globalTableIdentifier])) {
            return $this->requestedTableHeaders[$globalTableIdentifier];
        }


        if (!isset($this->requestedTables[$globalTableIdentifier])) {
            $this->loadTable($globalTableIdentifier);
        }

        return $this->requestedTables[$globalTableIdentifier]->getHeader();
    }

    /**
     * The UniversalInterpreter found a identifier for a table.
     * Can you give me the content of the table?
     *
     * @param string $globalTableIdentifier
     * @param UniversalFilterTableHeader $header The header you created using the above method.
     * @return UniversalTableContent
     */
    public function getTableContent($globalTableIdentifier, UniversalFilterTableHeader $header) {
        if (!isset($this->requestedTables[$globalTableIdentifier])) {
            $this->loadTableWithHeader($globalTableIdentifier, $header);
        }
        return $this->requestedTables[$globalTableIdentifier]->getContent();
    }

    /**
     * This method makes it possible to run a filter directly on the source.
     *
     * See documentation of the implemented interface for more information...
     *
     * @param UniversalFilterNode $query
     * @param string $sourceId
     * @return UniversalFilterNode
     */
    function runFilterOnSource(UniversalFilterNode $query, $sourceId) {
        /*
         * Check if resource (source) is queryable
         */
        $model = ResourcesModel::getInstance(Config::getConfigArray());

        $globalTableIdentifier = str_replace("/", ".", $sourceId);

        $identifierpieces = explode(".", $sourceId);
        array_push($identifierpieces, array());
        $package = strtolower($identifierpieces[0]);
        $resource = strtolower($identifierpieces[1]);

        // TODO allow for RESTparameters to be passed. So far no installed/core resource
        // implements IFilter though.
        // result is FALSE if the resource doesn't implement IFilter
        // result is the resourceObject on which to call and pass the filter upon if it does
        $result = null;
        try {
            $result = $model->isResourceIFilter($package, $resource);
        } catch (Exception $e) {
            return $query;
        }

        if ($result == FALSE) {
            return $query; //thereExistNoOptimalisationForThatSource
        } else {
//            execute partial trees on the source with id $sourceId
//                     (or the full query, if it can be converted completely)
//                     (not necessary the case: (even without joins and nested querys)
//                           e.g.: radius() is not a SQL function... etc...
//                              (bad example because you can convert it...)
//            for each partial answer the source can calculate {
//                convert it to a table $table
//                replace the calculated node in the query with a new ExternallyCalculatedFilterNode($table, $originalFilterNode);
//            }
            // not only contains the php object of the data but also
            // the node that has been executed and the index of it in its parent node.
            // if it has done the entire node (so the entire query has been done)
            // only a new Calculated node has to be passed, and $query doesnt have to be
            // adjusted, but replaced.
            // The convention to let this function know if the entire node has been executed is to pass
            // the index as -1
            // it could also be the case that the filter couldn't do anything with the query
            // this will be clear to this function if the resultObject->phpDataObject = NULL

            $resultObject = $model->readResourceWithFilter($query, $result);

            if (isset($resultObject->phpDataObject) && $resultObject->phpDataObject == NULL) {
                return $query;
            } elseif ($resultObject->indexInParent == "-1") {

                $converter = new PhpObjectTableConverter();
                $table = $converter->getPhpObjectTable($identifierpieces, $resultObject->phpDataObject);
                return new ExternallyCalculatedFilterNode($table, $query);
            } else {// query has been partially executed

                /*
                 * get the clauses from the resultObject
                 * then via the names of the clauses replace them in the query
                 */
                $query = $resultObject->query;

                /*
                 * iterate over the query tree
                 * Note that when the parentNode is null and you replace a node
                 * it means that you have executed the upper node of the query aka you've already executed the node.
                 *
                 * The index in the parent node is always 0 because we only execute clauses such as where, group by
                 * These nodes only have 1 source. We do not replace or partially execute binaryfunctions or joins, which have 2 or more sources.
                 */
                $parentNode = null;
                $currentNode = $query;
                $replaced = FALSE;
                $clause = $resultObject->clause;
                $phpObject = $resultObject->partialTreeResultObject;

                while ($currentNode != null && !$replaced) {
                    $type = $currentNode->getType();

                    switch ($clause) {
                        case "orderby":
                            if ($type == "FILTERSORTCOLUMNS") {
                                $this->replaceNodeInQuery($phpObject, $identifierpieces, $currentNode, $parentNode);
                                $replaced = TRUE;
                            }
                            break;
                        case "where":
                            if ($type == "FILTEREXPRESSION") {
                                $this->replaceNodeInQuery($phpObject, $identifierpieces, $currentNode, $parentNode);
                                $replaced = TRUE;
                            }
                            break;
                        case "groupby":
                            if ($type == "DATAGROUPER") {
                                /*
                                 * replace the query node
                                 */
                                $this->replaceNodeInQuery($phpObject, $identifierpieces, $currentNode, $parentNode);
                                $replaced = TRUE;
                            }
                            break;
                        case "select":
                            if ($type == "FILTERCOLUMN") {
                                /*
                                 * replace the query node
                                 */
                                $this->replaceNodeInQuery($phpObject, $identifierpieces, $currentNode, $parentNode);
                                $replaced = TRUE;
                            }
                            break;
                        case "limit":
                            if ($type == "FILTERLIMIT") {
                                /*
                                 * replace the query node
                                 */
                                $this->replaceNodeInQuery($phpObject, $identifierpieces, $currentNode, $parentNode);
                                $replaced = TRUE;
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
            }

            return $query;
        }
    }

    /**
     * This method is used in combination with runFilterOnSource.
     *
     * See documentation of the implemented interface for more information...
     *
     * @param string $globalTableIdentifier
     * @return string  A string representing a source.
     */
    public function getSourceIdFromIdentifier($globalTableIdentifier) {

        $splited = $this->splitIdentifier($globalTableIdentifier);
        return $splited[0] . "." . $splited[1];
    }

    /*
     * Replaces a node in the tree.
     */

    private function replaceNodeInQuery($phpObject, $identifierpieces, $currentNode, $parentNode) {
        /*
         * replace the query node
         */
        if ($phpObject != "") {
            $converter = new PhpObjectTableConverter();
            $table = $converter->getPhpObjectTable($identifierpieces, $phpObject);
            $replacementNode = new ExternallyCalculatedFilterNode($table, $currentNode);
            if ($parentNode != null) {
                $parentNode->setSource($replacementNode, 0);
            }
        }
    }

}

?>

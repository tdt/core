<?php

/**
 * This it the implementation of the TableManager for The-DataTank
 *
 * The TableManager makes it easier to view The DataTank as a collection of tables
 *
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 * @author Jan Vansteenlandt
 */

namespace tdt\core\spectql\implementation\tablemanager\implementation;

use tdt\core\datasets\DatasetController;
use tdt\core\definitions\DefinitionController;
use tdt\core\spectql\implementation\data\UniversalFilterTableHeader;
use tdt\core\spectql\implementation\data\UniversalFilterTableHeaderColumnInfo;
use tdt\core\spectql\implementation\sourcefilterbinding\ExternallyCalculatedFilterNode;
use tdt\core\spectql\implementation\tablemanager\IUniversalFilterTableManager;
use tdt\core\spectql\implementation\tablemanager\implementation\tools\PhpObjectTableConverter;
use tdt\core\spectql\implementation\tablemanager\implementation\UniversalFilterTableManager;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

class UniversalFilterTableManager implements IUniversalFilterTableManager {

    private static $IDENTIFIERSEPARATOR = ".";
    private static $HIERARCHICALDATESEPARATOR = ":";
    private $requestedTableHeaders = array();
    private $requestedTables = array();

    /**
     * Gets the resource as a php object
     * @param type $package
     * @param type $resource
     * @return type phpObject
     */
    private function getFullResourcePhpObject($package, $resource, $RESTparameters = array()) {

        $data_result = DatasetController::fetchData($package . '/' . $resource . '/' . implode('/', $RESTparameters));
        $data = $data_result->data;

        return $data;
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

        // Fetch the parts of the identifier, split them by using the hierarchical separator as the delimiter
        // TODO analyse if this is actually used
        $identifierparts = explode(UniversalFilterTableManager::$HIERARCHICALDATESEPARATOR, $globalTableIdentifier);

        // TODO isn't used?
        $hierarchicalsubparts = array();
        if (isset($identifierparts[1]) && strlen($identifierparts[1]) > 0) {
            $hierarchicalsubparts = explode(".", $identifierparts[1]);
        }

        // Retrieve the pieces of the uri that identify the resource
        $identifierpieces = explode(UniversalFilterTableManager::$IDENTIFIERSEPARATOR, $identifierparts[0]);

        $packageresourcestring = implode("/", $identifierpieces);

        $definition = DefinitionController::get($packageresourcestring);

        // Tell the user the resource could not be found when no definition is fetched
        if(empty($definition)){
            \App::abort(404, "The resource could not be found.");
        }

        // Retrieve the REST parameters of the identifier
        $rest_parameters = str_replace($definition->collection_uri . '/' . $definition->resource_name, '', $packageresourcestring);
        $rest_parameters = ltrim($rest_parameters, '/');
        $rest_parameters = explode('/', $rest_parameters);

        return array($definition->collection_uri, $definition->resource_name, $rest_parameters, $hierarchicalsubparts);
    }

    /**
     * Create a UniversalTable out of the PHP object identifed by $globalTableIdentifier
     */
    private function loadTable($globalTableIdentifier) {

        $splitedId = $this->splitIdentifier($globalTableIdentifier);

        $converter = new PhpObjectTableConverter();

        $resource = $this->getFullResourcePhpObject($splitedId[0], $splitedId[1], $splitedId[2]);

        // Convert the PHP object into a UniversalTable
        $table = $converter->getPhpObjectTable($splitedId, $resource);
        $this->requestedTables[$globalTableIdentifier] = $table;

        // Tell the table content it can't be destroyed just yet
        $table->getContent()->tableNeeded();
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
     * Return the UniversalFilterTableHeader based on the identifier of the table
     *
     * @param string $globalTableIdentifier
     * @return UniversalFilterTableHeader
     */
    public function getTableHeader($globalTableIdentifier) {

        $identifierpieces = $this->splitIdentifier($globalTableIdentifier);

        // Try fetching column names in case of a tabular resource.
        $columns = array();

        try {

            $definition = DefinitionController::get($this->getResourceIdentifier($globalTableIdentifier));
            $source = $definition->source()->first();

            $columns_collection = array();

            if(method_exists($source, 'tabularColumns')){
                $columns_collection = $source->tabularColumns()->getResults();
            }

            $columns = array();
            foreach($columns_collection as $collection_entry){
                array_push($columns, array("column_name" => $collection_entry["column_name"]));
            }

        } catch (Exception $e) {
            $columns = array();
        }

        if (!empty($columns) && !isset($this->requestedTableHeaders[$globalTableIdentifier])) {

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
            //TODO check if a filter can be resolved through the strategy itself.
            //$result = $model->isResourceIFilter($package, $resource);
        } catch (Exception $e) {
            return $query;
        }

        if ($result == FALSE) {
            return $query;
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

            if (empty($resultObject->indexInParent) && empty($resultObject->clause)){
                return $query;
            } elseif ($resultObject->indexInParent == "-1") {

                if(empty($resultObject->phpDataObject)){
                    $column_names = $model->getColumnsFromResource($package,$resource);
                    $arr = array();
                    $obj = new \stdClass();
                    foreach($column_names as $key => $column_name){
                        $obj->$column_name["column_name_alias"] = null;
                    }
                    array_push($arr,$obj);
                    $resultObject->phpDataObject = $arr;
                }

                $converter = new PhpObjectTableConverter();
                $table = $converter->getPhpObjectTable($identifierpieces, $resultObject->phpDataObject);
                return new ExternallyCalculatedFilterNode($table, $query);
            } else {// query has been partially executed

                if(empty($resultObject->partialTreeResultObject)){
                    $column_names = $model->getColumnsFromResource($package,$resource);
                    $arr = array();
                    $obj = new \stdClass();
                    foreach($column_names as $key => $column_name){
                        $obj->$column_name["column_name_alias"] = null;
                    }
                    array_push($arr,$obj);
                    $resultObject->partialTreeResultObject = $arr;
                }

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
                                $this->replaceNodeInQuery($phpObject, $identifierpieces, $currentNode, $parentNode);
                                $replaced = TRUE;
                            }
                            break;
                        case "select":
                            if ($type == "FILTERCOLUMN") {
                                $this->replaceNodeInQuery($phpObject, $identifierpieces, $currentNode, $parentNode);
                                $replaced = TRUE;
                            }
                            break;
                        case "limit":
                            if ($type == "FILTERLIMIT") {
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
     * Replaces a node in the AST
     */

    private function replaceNodeInQuery($phpObject, $identifierpieces, $currentNode, $parentNode) {

        // Replace the node in the AST
        if ($phpObject != "") {
            $converter = new PhpObjectTableConverter();
            $table = $converter->getPhpObjectTable($identifierpieces, $phpObject);
            $replacementNode = new ExternallyCalculatedFilterNode($table, $currentNode);
            if ($parentNode != null) {
                $parentNode->setSource($replacementNode, 0);
            }
        }
    }

    /**
     * Get the resource identifier based on the global identifier used in SPECTQL.
     */
    private function getResourceIdentifier($globalTableIdentifier){

        $identifierparts = explode(UniversalFilterTableManager::$HIERARCHICALDATESEPARATOR, $globalTableIdentifier);
        $hierarchicalsubparts = array();
        if (isset($identifierparts[1]) && strlen($identifierparts[1]) > 0) {
            $hierarchicalsubparts = explode(".", $identifierparts[1]);
        }

        $identifierpieces = explode(UniversalFilterTableManager::$IDENTIFIERSEPARATOR, $identifierparts[0]);

        $packageresourcestring = implode("/", $identifierpieces);

        return $packageresourcestring;
    }
}
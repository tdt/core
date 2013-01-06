<?php

include_once("universalfilter/common/BigDataBlockManager.class.php");
include_once("universalfilter/common/BigList.class.php");
include_once("universalfilter/common/BigMap.class.php");

include_once("universalfilter/data/UniversalFilterTableContentRow.class.php");
include_once("universalfilter/data/UniversalFilterTableHeader.class.php");
include_once("universalfilter/data/UniversalFilterTableContent.class.php");
include_once("universalfilter/data/UniversalFilterTable.class.php");
include_once("universalfilter/data/UniversalFilterTableHeaderColumnInfo.class.php");

/**
 * The TableManager is an abstraction that sees any data a collection of tables
 * 
 * You need to implement it to be able to use the universalfilter package...
 *
 * @package The-Datatank/universalfilter/tablemanager
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
interface IUniversalFilterTableManager {
    /**
     * The UniversalInterpreter found a identifier for a table. 
     * Can you give me the header of the table?
     * 
     * @param string $globalTableIdentifier
     * @return UniversalFilterTableHeader 
     */
    public function getTableHeader($globalTableIdentifier);
    
    /**
     * The UniversalInterpreter found a identifier for a table. 
     * Can you give me the content of the table?
     * 
     * @param string $globalTableIdentifier
     * @param UniversalFilterTableHeader $header The header you created using the above method.
     * @return UniversalTableContent 
     */
    public function getTableContent($globalTableIdentifier, UniversalFilterTableHeader $header);
    
    
    /**
     * This method makes it possible to run a filter directly on the source.
     * 
     * If a filter can not be implemented on a source, this method just returns the query. (so that's the default implementation)
     * 
     * If some parts of the query can be executed directly on the source:
     * This method is supposed to executes those parts on the source.
     * Then convert the answer to a UniversalTable.
     * And then in modify the query -> the parts that were executed are replaced with a ExternallyCalculatedFilterNode
     *   (new ExternallyCalculatedFilterNode($table, $originalFilterNode, ...);)
     *     where $originalFilterNode is the part of the query you replaced.
     *     and $table is the UniversalFilterTable
     *     and ... some information about how the columnMapping should happen... (by name or by index)
     * Thus, if the source supports everything the query contains, you just return a ExternallyCalculatedFilterNode.
     * 
     * This method gets called for each biggest part of the tree that can be executed on a single source.
     * Something belongs to "A single source" if getSourceIdFromIdentifier returns the same value for all identifiers.
     * 
     * @param UniversalFilterNode $query
     * @param string $sourceId
     * @return UniversalFilterNode 
     */
    function runFilterOnSource(UniversalFilterNode $query, $sourceId);
    
    /**
     * This method is used in combination with runFilterOnSource. 
     *   (read that documentation first)
     * 
     * For a certain table this method has to return a sourceId.
     * runFilterOnSource will then be called for the biggest subtree with the same sourceId.
     * 
     * This sourceId is also given as an argument to the runFilterOnSource
     * 
     * @param string $globalTableIdentifier 
     * @return string  A string representing a source.
     */
    public function getSourceIdFromIdentifier($globalTableIdentifier);
    
}

?>

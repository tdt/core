<?php

/**
 * This is an interface for strategies to implement if they support
 * native querying based on our abstract syntax tree, which is a tree
 * existing out of nodes who represent a certain query passed along with a resource request.
 *
 * @package The-Datatank/model/resources/read
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */


interface iFilter{
 
    /**
     * This has to return not only the dataset as a phpobject!!
     * Check the documentation in UniversalTableManager, above the function
     * runFilterOnSource, which explains what it expects from a resource if it can 
     * execute a query or a part of a query.
     * it expects a php object of your execution + the parent node and the index in that parent of the node 
     * you have executed! Basically what you provide is the information
     * to let us know what piece of the tree you have executed so that we can replace the subtree with a node
     * which let's us know that that particular part has been executed already.

     * $parameters is used to pass an array of extra parameters resources need
     * i.e. generic resources need a configObject
     * installed resources only need the query.
     */
    public function readAndProcessQuery($query,$parameters);
}
?>
<?php

// ----------------------------------------------------------------------------------
// Class: ClientQuery
// ----------------------------------------------------------------------------------

/**
 * ClientQuery Object to run a SPARQL Query against a SPARQL server.
 *
 * @version  $Id: ClientQuery.php 465 2007-06-27 16:47:57Z cweiske $
 * @author   Tobias Gau� <tobias.gauss@web.de>
 * @license http://www.gnu.org/licenses/lgpl.html LGPL
 *
 * @package sparql
 */
class ClientQuery extends Object {

    private $default = array();
    private $named = array();
    private $prefixes = array();
    private $query;

    /**
     * Adds a default graph to the query object.
     *
     * @param String  $default  default graph name
     */
    public function addDefaultGraph($default) {
        if (!in_array($this->named, $this->default))
            $this->default[] = $default;
    }

    /**
     * Adds a named graph to the query object.
     *
     * @param String  $default  named graph name
     */
    public function addNamedGraph($named) {
        if (!in_array($named, $this->named))
            $this->named[] = $named;
    }

    /**
     * Adds the SPARQL query string to the query object.
     *
     * @param String  $query the query string
     */
    public function query($query) {
        $this->query = $query;
    }

}

?>
<?php
require_once RDFAPI_INCLUDE_DIR . '/util/Object.php';

// ----------------------------------------------------------------------------------
// Class: Node
// ----------------------------------------------------------------------------------

/**
 * An abstract RDF node.
 * Can either be resource, literal or blank node.
 * Node is used in some comparisons like is_a($obj, "Node"),
 * meaning is $obj a resource, blank node or literal.
 *
 *
 * @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
 * @author Chris Bizer <chris@bizer.de>
 * @package model
 * @abstract
 *
 */
 class Node extends Object {
 } // end:RDFNode


?>
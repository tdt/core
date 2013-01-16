<?php
/**
*   Vocabulary
*
*   @version $Id: V0.9.7 2011-09-27 Update to PHP 5.3 for use in The DataTank(iRail) $
*   @author Tobias Gauß <tobias.gauss@web.de>
*   @package vocabulary
*
*/

// Include vocabularies
include_once (RDFAPI_INCLUDE_DIR . PACKAGE_RESMODEL);
require_once (RDFAPI_INCLUDE_DIR . 'vocabulary/ATOM_RES.php');
require_once (RDFAPI_INCLUDE_DIR . 'vocabulary/RDF_RES.php');
require_once (RDFAPI_INCLUDE_DIR . 'vocabulary/RDFS_RES.php');
require_once (RDFAPI_INCLUDE_DIR . 'vocabulary/OWL_RES.php');
require_once (RDFAPI_INCLUDE_DIR . 'vocabulary/DC_RES.php');
require_once (RDFAPI_INCLUDE_DIR . 'vocabulary/VCARD_RES.php');
require_once (RDFAPI_INCLUDE_DIR . 'vocabulary/FOAF_RES.php');
require_once (RDFAPI_INCLUDE_DIR . 'vocabulary/RSS_RES.php');
?>
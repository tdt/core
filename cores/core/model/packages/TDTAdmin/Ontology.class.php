<?php
/**
 * This class gives access to the ontology of resources
 *
 * @package The-Datatank/model/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl 
 * @license AGPLv3
 * @author Miel Vander Sande
 */
class TDTAdminOntology extends AReader {

    public static function getParameters(){
	return array();
    }

    public static function getRequiredParameters(){
	return array();
    }

    public function setParameter($key,$val){
        //we don't have any parameters
    }

    public function read(){
	return "Modify ontologies here";
    }

    public static function getDoc() {
        return "Lists a package ontology";
    }

}

?>

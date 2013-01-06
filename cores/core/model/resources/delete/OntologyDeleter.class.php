<?php
/**
 * This class OntologyDeleter deletes ontologies.
 * When deleting an ontology, we always expect a DELETE method!
 *
 * @package The-Datatank/model/resources/delete
 * @copyright (C) 2011 by iRail vzw/asbl 
 * @license AGPLv3
 * @author Miel Vander Sande
 */

class OntologyDeleter extends ADeleter{
    public function delete() {
        $package = array_shift($this->RESTparameters);
                
        if (count($this->RESTparameters) ==0){
            OntologyProcessor::getInstance()->deleteOntology($package);
        }else {
            $resource = implode("/", $this->RESTparameters);
            OntologyProcessor::getInstance()->deletePath($package, $resource);
        }
    }

}

?>

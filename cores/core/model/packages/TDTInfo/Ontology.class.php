<?php
/**
 * This class gives access to the ontology of resources
 *
 * @package The-Datatank/model/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl 
 * @license AGPLv3
 * @author Miel Vander Sande
 */
class TDTInfoOntology extends AReader {

    private $ontology;
    private $ont_package;

    public function __construct($package, $resource, $RESTparameters) {
        parent::__construct($package, $resource, $RESTparameters);
    }

    public static function getParameters() {
        return array("package" => "Name of a package that needs to be analysed, must be set !",
            "resource" => "Name of a resource within the given package, is not required.",
        );
    }

    public static function getRequiredParameters() {
        return array("package");
    }

    public function read() {
        $this->getData();
        return $this->ontology;
    }

    public function setParameter($key, $val) {
        if ($key == "package") {
            $this->ont_package = $val;
        }
    }

    public static function getAllowedFormatters() {
        return array();
    }

    private function getData() {
        if (count($this->RESTparameters) == 0) {
            //Create empty ontology for a package
            $this->ontology = OntologyProcessor::getInstance()->readOntology($this->ont_package);
        } else {
            $resource = implode("/", $this->RESTparameters);
            $this->ontology = OntologyProcessor::getInstance()->readPath($this->ont_package, $resource);
        }
    }

    public static function getDoc() {
        return "Lists a package ontology";
    }

}

?>

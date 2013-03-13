<?php

/**
 * This class handles Turtle input
 *
 * @copyright (C) 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */

namespace tdt\core\strategies;
use tdt\exceptions\TDTException;
use tdt\core\model\resources\AResourceStrategy;

class RDFXML extends AResourceStrategy {

    public function read(&$configObject, $package, $resource) {
        $parser = \ARC2::getRDFXMLParser();
        $data = \tdt\core\utility\Request::http($configObject->uri);
        $parser->parse("",$data->data);

        return $parser;
    }

    public function isValid($package_id, $generic_resource_id) {
        $parser = \ARC2::getRDFXMLParser();
        $parser->parse($this->uri);

        if (!$parser)
            throw new TDTException(500, array("Could not transform the RDF/XML data from " . $this->uri . " to a ARC model, please check if the RDF/XML is valid."), $exception_config);
        
        return true;
    }

    /**
     * The parameters returned are required to make this strategy work.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateRequiredParameters() {
        return array("uri");
    }

    public function documentReadRequiredParameters() {
        return array();
    }

    public function documentUpdateRequiredParameters() {
        return array();
    }

    public function documentCreateParameters() {
        return array(
            "uri" => "The URI of the RDF/XML file.",
        );
    }

    public function documentReadParameters() {
        return array();
    }

    public function documentUpdateParameters() {
        return array();
    }

    public function getFields($package, $resource) {
        return array();
    }

}
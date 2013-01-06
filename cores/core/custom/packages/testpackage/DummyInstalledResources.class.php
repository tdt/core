<?php

/**
 * This is a class which will return all the possible admin calls to this datatank
 * 
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 */
class DummyInstalledResource extends AReader {

    public static function getParameters() {
        return array();
    }

    public static function getRequiredParameters() {
        return array();
    }

    public function setParameter($key, $val) {
        //we don't have any parameters
    }

    public static function getDoc() {
        return "This resource is a dummy resource.";
    }

    public function read() {
        $dummy = new stdClass();
        $dummy->datamember = "le datamember";
        return $dummy;
    }

}

?>

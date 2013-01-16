<?php
/**
 * Doc is a visitor that will visit every ResourceFactory and ask for their documentation. It is cached because this process is quite heavy.
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 */


namespace tdt\core\model;

use tdt\core\formatters\FormatterFactory;
use tdt\core\model\CoreResourceFactory;
use tdt\core\model\DBQueries;
use tdt\framework\Cache\Cache;
use tdt\framework\Config;

class Doc{

    /*
     * installation variables
     */
    private $hostname;
    private $subdir;

    public function __construct() {
        $this->hostname = Config::get("general","hostname");
        $this->subdir = Config::get("general","subdir");
    }

    /**
     * This function will visit any given factory and ask for the documentation of the resources they're responsible for.
     * @return Will return the entire documentation array which can be used by TDTInfo/Resources. It can also serve as an internal checker for availability of packages/resources
     */
    public function visitAll($factories){
        $c = Cache::getInstance();
        $doc = $c->get($this->hostname. $this->subdir . "documentation");
        if(is_null($doc)){
            $doc = new \stdClass();
            foreach($factories as $factory){
                $factory->makeDoc($doc);
            }
            $c->set($this->hostname. $this->subdir . "documentation",$doc,60*60*60); // cache it for 1 hour by default
        }
        return $doc;
    }

    /**
     * This function returns all packages present in the datatank
     */
    public function visitAllPackages(){
        $c = Cache::getInstance();
        $doc = $c->get($this->hostname. $this->subdir . "packagedocumentation");
        if(is_null($doc)){
            $doc = new \stdClass();
            $packages = DBQueries::getAllPackages();
            foreach($packages as $package){
                $packagename = $package->package_name;
                $doc->$packagename = new \stdClass();
            }

            $coreResourceFactory = new CoreResourceFactory();
            $packages = $coreResourceFactory->getAllPackagesDoc();

            foreach($packages as $package){
                $doc->$package = new \stdClass();
            }

            $c->set($this->hostname. $this->subdir . "packagedocumentation",$doc,60*60*60); // cache it for 1 hour by default
        }
        return $doc;

    }

    /**
     * This function will visit any given factory and ask for the description of the resources they're responsible for.
     * @return Will return the entire description array which can be used by TDTAdmin/Resources.
     */
    public function visitAllDescriptions($factories){
        $c = Cache::getInstance();
        $doc = $c->get($this->hostname. $this->subdir . "descriptiondocumentation");
        if(is_null($doc)){
            $doc = new \stdClass();
            foreach($factories as $factory){
                $factory->makeDescriptionDoc($doc);
            }
            $c->set($this->hostname. $this->subdir . "descriptiondocumentation",$doc,60*60*60); // cache it for 1 hour by default
        }
        return $doc;
    }

    /**
     * Visits all the factories in order to get the admin documentation, which elaborates on the admin functionality
     * @return $mixed  An object which holds the documentation on how to perform admin functions such as creation, deletion and updates.
     */
    public function visitAllAdmin($factories){
        $c = Cache::getInstance();
        $doc = $c->get($this->hostname. $this->subdir. "admindocumentation");
        if(is_null($doc)){
            $doc = new \stdClass();
            foreach($factories as $factory){
                $factory->makeDeleteDoc($doc);
                $factory->makeCreateDoc($doc);
                $factory->makeUpdateDoc($doc);
            }
            $c->set($this->hostname. $this->subdir . "admindocumentation",$doc,60*60*60); // cache it for 1 hour by default
        }
        return $doc;
    }

    /**
     * Gets the documentation on the formatters
     * @return $mixed An object which holds the documentation about all the formatters.
     */
    public function visitAllFormatters(){
        $c = Cache::getInstance();
        $doc = $c->get($this->hostname. $this->subdir . "formatterdocs");
        $ff = FormatterFactory::getInstance();
        if(is_null($doc)){
            $doc = $ff->getFormatterDocumentation();
            $c->set($this->hostname. $this->subdir . "formatterdocs",$doc,60*60*60);
        }
        return $doc;
    }

    /**
     * Gets the documentation on the visualizations
     * @return $mixed An object which holds the information about the visualizations
     */
    public function visitAllVisualizations(){
        $c = Cache::getInstance();
        $doc = $c->get($this->hostname. $this->subdir . "visualizationdocs");
        $ff = FormatterFactory::getInstance();
        if(is_null($doc)){
            $doc = $ff->getVisualizationDocumentation();
            $c->set($this->hostname. $this->subdir . "visualizationdocs",$doc,60*60*60);
        }
        return $doc;
    }
}
?>

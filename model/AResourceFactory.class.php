<?php
/**
 * Interface for a factory: check documentation on the Abstract Factory Pattern if you don't understand this code.
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 */

namespace tdt\core\model;

abstract class AResourceFactory{
      
    
    /**
     * Quickly check if this factory has a specific resource
     * @param package the name of the package the resource has
     * @param resource the name of the resource
     * @return boolean whether or not this factory has the package
     */
    public function hasResource($package,$resource){
        foreach($this->getAllResourceNames() as $packagename => $resourcenames){
            foreach($resourcenames as $resourcename){
                if($resourcename == $resource && $package == $packagename){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get all of the available resources (their names) and return them in an array.
     */
    abstract protected function getAllResourceNames();

    /**
     * Creates an instance of a creator class.
     * @param $package the new package of the resource. It may exist already
     * @param $resource the name of the new resource. If it exists already, an exception will be thrown
     * @return The returned class extends ACreator and can add a resource to the system
     */
    abstract public function createCreator($package,$resource, $parameters, $RESTparameters);

    /**
     * Creates an instance of a reader. This can return the right information for a request
     * @param $package the package of the requested resource.
     * @param $resource the name of the requested resource.
     * @return The returned class extends AReader and can read information from a Resource
     */
    abstract public function createReader($package, $resource, $parameters, $RESTparameters);

    /**
     * Creates an instance of a deleter class.
     * @param $package the package of the resource. It will not get deleted
     * @param $resource the name of the new resource.
     * @return The returned class extends ADeleter and can delete a resource from the system
     */
    abstract public function createDeleter($package,$resource, $RESTparameters);

    /**
     * Visitor pattern function: This is the documentation on how to call a read resource
     * @param $doc Doc is an instance of the Doc class. It will go allong every factory and ask for every resource's documentation data. Each resource adds what it wants to add.
     */
    abstract public function makeDoc($doc);

     /**
     * Visitor pattern function: This is the documentation on what all of the description properties of a resource are
     * @param $doc Doc is an instance of the Doc class. It will go allong every factory and ask for every resource's documentation data. Each resource adds its full description.
     */
    abstract public function makeDescriptionDoc($doc);


    /**
     * Visitor pattern function
     * @param $doc Doc is an instance of the Doc class. It will go allong every factory and ask for every resource's documentation data. Each resource adds what it wants to add.
     */
    abstract public function makeDeleteDoc($doc);

    /**
     * Visitor pattern function
     * @param $doc Doc is an instance of the Doc class. It will go allong every factory and ask for every resource's documentation data. Each resource adds what it wants to add.
     */
    abstract public function makeCreateDoc($doc);

/**
     * Visitor pattern function
     * @param $doc Doc is an instance of the Doc class. It will go allong every factory and ask for every resource's documentation data. Each resource adds what it wants to add.
     */
    public function makeUpdateDoc($doc){
        
    }
}
?>

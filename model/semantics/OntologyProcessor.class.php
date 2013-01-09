<?php

/**
 * This class OnthologyProcessor handles actions on an ontology of a resource.
 * I supplies methods for mapping data members of resources to external ontology classes or properties. 
 *
 * Includes RDF Api for PHP <http://www4.wiwiss.fu-berlin.de/bizer/rdfapi/>
 * Licensed under LGPL <http://www.gnu.org/licenses/lgpl.html>
 * 
 * @package The-Datatank/model/semantics
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */
include_once("tdtml/TDTML.class.php");

class OntologyProcessor {

    private static $uniqueinstance;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (!isset(self::$uniqueinstance)) {
            self::$uniqueinstance = new OntologyProcessor();
        }
        return self::$uniqueinstance;
    }

    /**
     *
     * This function reads an ontology turtle file and loads the content in the ontology.
     *
     *
     * @param string $package
     * @param string $filename 
     * @access public
     */
    public function readOntologyFile($package, $filename) {
        $this->getModel($package)->load($filename, "n3");
    }

    /**
     * Checks if an object of type Model describes an ontology
     * 
     * @param Model $model Model instance containing RDF triples
     * @return boolean Returns true if the model describes an ontology 
     * @access public
     */
    public function isOntology($model) {
        $result = $model->findFirstMatchingStatement(null, RDF::TYPE(), OWL::ONTOLOGY());

        if (is_null($result))
            return false;
        else
            return true;
    }

    /**
     * Checks if a package already has an ontology
     * 
     * @param string $package The package that needs checking
     * @return boolean Returns true if there an ontology of this package exists
     * @access public
     */
    public function hasOntology($package) {
        return RbModelFactory::getRbStore()->modelExists($this->getOntologyURI($package));
    }

    //CRUD METHODS for whole Ontology

    public function updateOntology($package) {
        //Don't know if this will ever have an implementation
    }

    /**
     * Creates an ontology of a package. If a turtle file is supplied, the file is parsed into the ontology.
     * 
     * @param string $package The package for which we want to create an ontology
     * @param string $file The uri to a turtle file describing the ontology
     */
    public function createOntology($package, $file=null) {
        if (isset($file) && !is_null($file)) {
            //file_exists returns false with correct files, probably not accessible?
            //if (file_exists($file))
            $this->readOntologyFile($package, $file);
        }else
            $this->getModel($package);
    }

    /**
     * Reads the entire ontology of a package.
     * 
     * @param string $package The package of which we want to read the ontology
     * @return MemModel The object containing the resulting RDF model 
     */
    public function readOntology($package) {
        return $this->getModel($package)->getMemModel();
    }

    /**
     * Deletes the entire ontology of a package
     * 
     * @param string $package The package of which we want to delete the ontology
     */
    public function deleteOntology($package) {
        $this->getModel($package)->delete();
    }

    //CRUD METHODS for paths in Ontology

    /**
     * Adds a mapping to a specific Class or Property in the ontology
     * 
     * @param string $package The package containing the resource
     * @param string $path The classpath of the class or property in the resource
     * @param string $value The name of the external class or property 
     * @param string $nmsp The namespace URI of the external ontology
     * @param string $prefix The prefix of the external ontology namespace
     */
    public function updatePathMap($package, $path, $value, $nmsp, $prefix=null) {
        $model = $this->getModel($package);

        $resource = new Resource($path);
        $mapping = new Resource($nmsp . $value);

        if (!is_null($prefix))
            $model->addNamespace($prefix, $nmsp);

        $statement = null;
        if ($this->isPathProperty($package, $path))
            $statement = new Statement($resource, OWL::EQUIVALENT_PROPERTY(), $mapping);
        else
            $statement = new Statement($resource, OWL::EQUIVALENT_CLASS(), $mapping);

        $this->getModel($package)->add($statement);
    }

    /**
     * Determines which mapping is preffered over the others.
     * 
     * @param string $package The package containing the resource
     * @param string $path The classpath of the class or property in the resource
     * @param string $value The name of the external class or property 
     * @param string $nmsp The namespace URI of the external ontology
     * @param string $prefix The prefix of the external ontology namespace
     */
    public function updatePathPreferredMap($package, $path, $value, $nmsp) {
        $model = $this->getModel($package);

        $resource = new Resource($path);
        $mapping = new Resource($nmsp . $value);

        $statement = $this->getModel($package)->findFirstMatchingStatement($resource, TDTML::PREFERREDMAP(), null);
        if (!is_null($statement))
            $this->getModel($package)->remove($statement);

        $statement = new Statement($resource, TDTML::PREFERREDMAP(), $mapping);
        $this->getModel($package)->add($statement);
    }

    /**
     * Deletes a mapping from a specific Class or Property in the ontology
     * 
     * @param string $package The package containing the resource
     * @param string $path The classpath of the class or property in the resource
     * @param string $value The name of the external class or property 
     * @param string $nmsp The namespace URI of the external ontology
     */
    public function updatePathDeleteMap($package, $path, $value, $nmsp) {
        $model = $this->getModel($package);

        $resource = new Resource($path);
        $mapping = new Resource($nmsp . $value);

        $type = null;
        if ($this->isPathProperty($package, $path))
            $type = OWL::EQUIVALENT_PROPERTY();
        else
            $type = OWL::EQUIVALENT_CLASS();

        $statement = $this->getModel($package)->findFirstMatchingStatement($resource, $type, $mapping);
        $this->getModel($package)->remove($statement);

        $statement = $this->getModel($package)->findFirstMatchingStatement($resource, TDTML::PREFERREDMAP(), $mapping);
        if (!is_null($statement))
            $this->getModel($package)->remove($statement);
    }

    /**
     * Creates an ontology entry for a property.
     * 
     * @param string $package The package containing the resource
     * @param string $path The classpath of the property.
     */
    public function createPropertyPath($package, $path) {
        $resource = new Resource($path);
        $statement = new Statement($resource, RDF::TYPE(), RDF::PROPERTY());
        $this->getModel($package)->add($statement);
    }

    /**
     * Creates an ontology entry for a class.
     * 
     * @param string $package The package containing the resource
     * @param string $path The classpath of the class.
     * @access public
     */
    public function createClassPath($package, $path) {
        $resource = new Resource($path);
        $statement = new Statement($resource, RDF::TYPE(), OWL::OWL_CLASS());
        $this->getModel($package)->add($statement);
    }

    /**
     * Reads the ontology only containing statements concerning the supplied classpath.
     * 
     * @param string $package The package containing the resource
     * @param string $path The classpath of the member
     * @return MemModel The model containing the resulting triples
     * @access public
     */
    public function readPath($package, $path) {
        $param = str_replace('/', '\/', $path) . '%';
        $model = $this->getModel($package)->findWildcarded($param, null, null);

        $base_resource = new Resource($model->getBaseURI() . $path);
        $description = new Literal("Ontology of " . $package . "/" . $path . " in The DataTank", null, 'datatype:STRING');
        $model->add(new Statement($base_resource, RDF::TYPE(), OWL::ONTOLOGY()));
        $model->add(new Statement($base_resource, RDFS::COMMENT(), $description));
        return $model;
    }

    /**
     * Deletes a data member from the ontology
     *
     * @param type $package
     * @param type $path 
     * @access public
     */
    public function deletePath($package, $path) {
        $temp = $this->readPath($package, $path);
        foreach ($temp->triples as $statement) {
            echo $this->getModel($package)->remove($statement);
        }
    }

    /*
     * This function retrieves all triples containing mapping.
     * 
     * @param string $package The package to get mappings from
     * @return Mixed Returns array with triples or false when there are no results
     * @access public
     */

    public function getMapping($package) {
        $ontology = $this->getModel($package);
        
        $classes = $ontology->find(null, OWL::EQUIVALENT_CLASS(), null);
        $properties = $ontology->find(null, OWL::EQUIVALENT_PROPERTY(), null);
        $result = array_merge($classes->triples, $properties->triples);
        
        $preferred = $ontology->find(null, TDTML::PREFERREDMAP(), null);
        $preferred = $preferred->triples;

        $namespaces = $ontology->getParsedNamespaces();

        $mapping = array();

        foreach ($preferred as $triple) {
            $temp = new stdClass();
            $temp->map = $triple->getObject()->getURI();

            $namespace = $triple->getObject()->getNamespace();
            $temp->prefix = $namespaces[$namespace];
            $temp->nmsp = $namespace;

            $mapping[$triple->getSubject()->getURI()] = $temp;
        }

        foreach ($result as $triple) {
            if (!array_key_exists($triple->getSubject()->getURI(), $mapping)) {
                $temp = new stdClass();
                $temp->map = $triple->getObject()->getURI();

                $namespace = $triple->getObject()->getNamespace();
                $temp->prefix = $namespaces[$namespace];
                $temp->nmsp = $namespace;

                $mapping[$triple->getSubject()->getURI()] = $temp;
            }
        }
        if (count($mapping) > 0)
            return $mapping;

        return false;
    }

    /**
     * Automatically generates a data model in the ontology based on the known dataset scheme.
     *
     * @param string $package The packase of the resource
     * @param string $resource The resource to create an ontology for
     */
    public function generateOntology($package, $resource, $fields) {
        $model = $this->getModel($package); //Create an empty model
        //Check if resource is generic
        //if so we can autogenerate the ontology from getFields in the strategy
        if (!is_null($fields)) {
            $model->add(new Statement(new Resource($resource), RDF::TYPE(), OWL::OWL_CLASS()));
            //Add stdClass wrapper, since this is for now always the case
            $model->add(new Statement(new Resource($resource . '/stdClass'), RDF::TYPE(), OWL::OWL_CLASS()));
            //iterate the fields and add them as properties
            foreach ($fields as $field) {
                $model->add(new Statement(new Resource($resource . '/stdClass/' . $field), RDF::TYPE(), RDF::PROPERTY()));
            }
        }
    }

    /**
     * Function retrieving the unique URI for the package ontology
     */
    public function getOntologyURI($package) {
        return Config::$HOSTNAME . Config::$SUBDIR . 'TDTInfo/Ontology/' . $package . '/';
    }

    /**
     * 
     *
     * @return array Returns an array listing all the available ontology models 
     */
    public function getAllOntologys() {
        return RbModelFactory::getRbStore()->listModels();
    }

    //Private Methods
    /**
     *  Creates and returns an new RDF model and stores it in the database.
     * 
     * @param string $package
     * @return RbModel Returns RedBean RDF model object that is persitent with the model in the database
     */
    private function getModel($package) {
        $store = RbModelFactory::getRbStore();
        //gets the model if it exist, else make a new one. Either way, it's the right one.
        //Gets a ResModel containing an RbModel, which doesn't store statements in memory, only in db.
        $model = RbModelFactory::getRbModel($store, $this->getOntologyURI($package));

        $resource = new Resource($this->getOntologyURI($package));
        $literal = new Literal("Ontology of the " . $package . " package in The DataTank", "en", "datatype:STRING");
        $model->add(new Statement($resource, RDF::TYPE(), OWL::ONTOLOGY()));
        $model->add(new Statement($resource, RDFS::COMMENT(), $literal));
        $model->setBaseUri($this->getOntologyURI($package));

        return $model;
    }

    /**
     * Checks in the ontology if a certain classpath is a property
     * 
     * @param string $package The package of the resource
     * @param string $path The classpath of the resource
     * @return boolean  Returns true if the classpath is a property
     */
    private function isPathProperty($package, $path) {
        //Can we find a type for this path?
        $statement = $this->getModel($package)->findFirstMatchingStatement(new Resource($path), RDF::TYPE(), null);
        var_dump($package);
        var_dump($path);
        if (is_null($statement)) {
            //if not, there is no entry and no mapping can be done
            throw new OntologyPathDoesntExistTDTException($path . " cannot be found in ontology");
        } else if ($statement->getObject()->equals(RDF::PROPERTY())) {
            return true;
        } else {
            return false;
        }
    }

}

?>
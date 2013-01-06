<?php

/**
 * This class generates RDF output for the retrieved data using the stored mapping.
 * 
 * Includes RDF Api for PHP <http://www4.wiwiss.fu-berlin.de/bizer/rdfapi/>
 * Licensed under LGPL <http://www.gnu.org/licenses/lgpl.html>
 *
 * @package The-Datatank/model/semantics
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */
class RDFOutput {

    private $model;
    private $package;
    private $mapping;

    /*
     * Constructor
     */

    public function __construct() {
        //create a memory model to serialise
        $this->model = ModelFactory::getResModel(MEMMODEL);
        $this->package = RequestURI::getInstance()->getPackage();

        $this->mapping = OntologyProcessor::getInstance()->getMapping($this->package);
    }

    /**
     * Analyzes a dataobject and parses it into an RDF model.
     *
     * @param	object $object
     * @return	MemModel Returns an RDF model of $object
     * @access	public
     */
    public function buildRdfOutput($object) {
        //Get the different parts of the requested path
        $arr = explode('/', RequestURI::getInstance()->getResourcePath());


        $beginpath = '';
        while (count($arr) > 1) {
            $item = array_shift($arr);

            //numeric means array of stdClass, add stdClass to the classpath, else add the item
            if (!is_numeric($item))
                $beginpath .= $item . '/';
            else
                $beginpath .= 'stdClass' . '/';
        }

        foreach ($object as $property => $value) {
            //if the property is the same as the resource, we request the whole resource.
            //In this case we need to add it seperatly to the classpath, else it is left out.
            if ($property == RequestURI::getInstance()->getResource())
                $beginpath .=$property . '/';

            //start analyzing the data
            $this->analyzeVariable($object->$property, RequestURI::getInstance()->getRealWorldObjectURI(), $beginpath);
        }

        return $this->model->getModel();
    }

    /**
     * Recursive function for analyzing an object and building its instance path and classpath.
     * The uri is used for creating RDF instances, while the classpath is used for retrieving Ontology mapping.
     *
     * @param Mixed $var The current object that is being analyzed
     * @param string $uri The current uri of the object
     * @param string $path The current classpath of the object
     * @param ResResource $resource The parent resource for the current object
     * @param ResProperty $property The parent property for the current object
     * @access private
     */
    private function analyzeVariable($var, $uri='', $path='', $resource = null, $property=null) {
        //Check if the object is an array, object or primitive variable
        if (is_array($var) && !TDT::is_assoc($var)) {

            //Temporarily store the uri path of this array
            $temp = $uri;
            //An indexed array is turned into a rdf sequence
            $res = $this->getList($uri);

            //Iterate all the values in the array, extend the uri and start over.
            for ($i = 0; $i < count($var); $i++) {
                $uri = $temp;
                $this->analyzeVariable($var[$i], $uri . '/' . $i, $path, $res);
            }
            //Check if the array is associative. If so, treat like an object.
        } else if (is_object($var) || TDT::is_assoc($var)) {
            //If this is not an array, it's an object, else an associative array
            if (!is_array($var))
                $path .= get_class($var);
            else
            //When it is an associative array, we need to use the property name instead of the classname
            //this is added later in the foreach, so delete the slash
                $path = substr($path, 0, strlen($path) - 1);

            $temp = $uri;
            $temp2 = $path;
            //create a resource of this array using the build uri path
            $res = $this->getClass($uri, $path);

            //Add this resource to the parent resource
            $this->addToResource($resource, $property, $res);

            //iterate all the key/value pairs, extend the uri and create a property from the key
            foreach ($var as $key => $value) {
                $path = $temp2;
                $uri = $temp;
                $prop = $this->getProperty($key, $path);

                //When it is an assoc array, add the property name now. 
                //In case of an object, it is done in the next iteration
                if (!is_object($value))
                    $path .= '/' . $key;

                //start over for each value
                $this->analyzeVariable($value, $uri . '/' . $key, $path . '/', $res, $prop);
            }
        } else {
            //Variable is a primitive type, so create typed literal.
            $lit = $this->getLiteral($var);
            $this->addToResource($resource, $property, $lit);

            $path = '';
            $uri = '';
        }
    }

    /**
     * Adds a resource to another resource with a property, thus creating a triple.
     *
     * @param Resource $resource The parent resource to add property to. This can be a ResResource or a ResList
     * @param ResProperty $property The property to be added to the resource
     * @param Resource $object The object of the property. This can be a resResource or a ResLiteral
     * @access private
     */
    private function addToResource($resource, $property, $object) {
        //Check if there is aready parent resource. If not, this resource is probably the first one.
        if (!is_null($resource)) {
            //If the resource is a list, just add the object to it, property is not important
            if (is_a($resource, 'ResList')) {
                $resource->add($object);
            } else if (is_a($resource, 'ResResource'))
                $resource->addProperty($property, $object);
        }
    }

    /*
     * Creates a rdf:List for the ResModel
     * 
     * @param string $uri The instance uri to create a rdf:List for
     * @return ResList Object representing the list
     * @access private
     */

    private function getList($uri) {
        $res = $this->model->createList($uri);
        $res->addProperty(RDF_RES::TYPE(), RDF_RES::RDF_LIST());

        return $res;
    }

    /*
     * Get a property mapped to an ontology. If no mapping is present, create a non-existing property from name.
     * 
     * @param string $name name of the property
     * @param string $path Hierarchical path of data struture
     * 
     * @return ResProperty Returns the created,mapped property
     * @access private
     */

    private function getProperty($name, $path) {
        $path .= '/' . $name;
        if ($this->mapping) {
            if (array_key_exists($path, $this->mapping)) {
                $this->model->addNamespace($this->mapping[$path]->prefix, $this->mapping[$path]->nmsp);
                return $this->model->createProperty($this->mapping[$path]->map);
            }
        }
        return $this->model->createProperty(OntologyProcessor::getInstance()->getOntologyURI($this->package) . $name);
    }

    /*
     * Get a resource with mapped class as type. If no mapping is present, no type is given.
     * 
     * @param string $uri Instance URI of this resource
     * @param string $path Hierarchical path of data struture
     * 
     * @return ResResource Returns the created, mapped resource.
     * @access private
     */

    private function getClass($uri, $path) {
        $resource = $this->model->createResource($uri);

        if ($this->mapping) {
            if (array_key_exists($path, $this->mapping)) {
                $resource->addProperty(RDF_RES::TYPE(), new ResResource($this->mapping[$path]->map));
                $this->model->addNamespace($this->mapping[$path]->prefix, $this->mapping[$path]->nmsp);
            }
        }
        return $resource;
    }

    /**
     *  Map the datatype of a primitive type to the right indication string for RAP API
     *  and return a literal
     *  Datatypes are found in rdfapi-php/api/constants.php.
     *
     * @param	string $value String value to be turned into a literal
     * @return  ResLiteral Literal containing the value
     * @access	private
     */
    private function getLiteral($value) {
        $type = DATATYPE_SHORTCUT_PREFIX;
        if (is_int($value))
            $type .= 'INT';
        else if (is_bool($value))
            $type .= 'BOOLEAN';
        else if (is_float($value))
            $type .= 'DECIMAL';
        else
            $type .= 'STRING';

        return $this->model->createTypedLiteral($value, $type);
    }

}

?>

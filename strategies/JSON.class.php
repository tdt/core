<?php

/**
 * This class handles a JSON query
 *
 * @package The-Datatank/custom/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */

namespace tdt\core\strategies;

use tdt\core\model\DBQueries;
use tdt\core\model\resources\AResourceStrategy;
use tdt\core\model\resources\GenericResource;
use tdt\framework\TDTException;
use RedBean_Facade as R;

class JSON extends AGenericStrategy {

    //put your code here

    protected $parameters = array(); // create parameters
    protected $updateParameters = array(); // update parameters

    /**
     * The parameters returned are required to make this strategy work.
     * @return array with parameter => documentation pairs
     */

    public function documentCreateRequiredParameters() {
        return array("uri");
    }

    /**
     * The parameters ( array keys ) returned all of the parameters that can be used to create a strategy.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateParameters() {
        $this->parameters["uri"] = "The URI to the JSON file.";
        return $this->parameters;
    }

    // fill in the configuration object that the strategy will receive
    public function read(&$configObject, $package, $resource) {
        parent::read($configObject, $package, $resource);

        /**
         * check if the uri is valid ( not empty )
         */
        if (isset($configObject->uri)) {
            $filename = $configObject->uri;
        } else {
            throw new TDTException(452, array("Can't find URI of the JSON data"));
        }


        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $filename);
        curl_setopt($ch, CURLOPT_HEADER, 0);
            
        // grab URL and pass it to the browser
        $json = curl_exec($ch);

        // close cURL resource, and free up system resources
        curl_close($ch);

        return json_decode($json);
    }

}

?>

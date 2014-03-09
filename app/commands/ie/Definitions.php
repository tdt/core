<?php

namespace tdt\commands\ie;

use tdt\core\definitions\DefinitionController;

/**
 * Import/export definitions
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class Definitions implements IImportExport
{

    public static function import($data)
    {

        $definitions = $data['definitions'];
        $username = $data['username'];
        $password = $data['password'];

        // Basic auth header
        $auth_header = "Basic " . base64_encode(trim($username) . ":" . trim($password));

        $messages = array();

        foreach ($definitions as $identifier => $definition_params) {

            $headers = array(
                            'Content-Type' => 'application/tdt.definition+json',
                            'Authorization' => $auth_header,
                        );

            self::updateRequest('PUT', $headers, $definition_params);

            // Add the new definition
            $response = DefinitionController::handle($identifier);
            $status_code = $response->getStatusCode();

            $messages[$identifier] = ($status_code == 200);
        }

        return $messages;

    }

    public static function export($identifier = null)
    {
        if (empty($identifier)) {
            // Request all of the definitions
            return DefinitionController::getAllDefinitions();
        } else {
            // Request a single definition
            $definition =  DefinitionController::get($identifier);
            return array($identifier => $definition->getAllParameters());
        }
    }


    /**
     * Custom API call function
     */
    public static function updateRequest($method, $headers = array(), $data = array())
    {

        // Set the custom headers.
        \Request::getFacadeRoot()->headers->replace($headers);

        // Set the custom method.
        \Request::setMethod($method);

        // Set the content body.
        if (is_array($data)) {
            \Input::merge($data);
        }
    }
}

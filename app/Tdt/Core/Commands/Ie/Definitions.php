<?php

namespace Tdt\Core\Commands\Ie;

use Tdt\Core\Repositories\DefinitionRepository;
use Tdt\Core\Definitions\DefinitionController;

/**
 * Import/export definitions
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class Definitions implements IImportExport
{

    protected $definition_controller;

    public function __construct()
    {
        $this->definition_controller = \App::make('Tdt\Core\Definitions\DefinitionController');
        $this->definitions = \App::make('Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface');
    }

    public function import($data)
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
            $response = $this->definition_controller->handle($identifier);
            $status_code = $response->getStatusCode();

            $messages[$identifier] = ($status_code == 200);
        }

        return $messages;

    }

    public function export($identifier = null)
    {
        if (empty($identifier)) {
            // Request all of the definitions
            return $this->definitions->getAllFullDescriptions();
        } else {
            // Request a single definition
            $definition =  $this->definitions->getFullDescription($identifier);
            return array($identifier => $definition);
        }
    }


    /**
     * Custom API call function
     */
    public function updateRequest($method, $headers = array(), $data = array())
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

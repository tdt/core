<?php

namespace tdt\core\definitions;

use Illuminate\Routing\Router;
use tdt\core\auth\Auth;

/**
 * LicenseController: Controller that handels the available licenses for the dcat
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class LicenseController extends \Controller {

    public static function handle($uri){

        // Set permission
        Auth::requirePermissions('info.view');

        // Get extension (if set)
        $extension = (!empty($matches[2]))? $matches[2]: null;

        // Propagate the request based on the HTTPMethod of the request
        $method = \Request::getMethod();

        switch($method){
            case "GET":
                return self::getLicenses($uri);
                break;
            default:
                // Method not supported
                \App::abort(405, "The HTTP method '$method' is not supported by this resource.");
                break;
        }
    }

    /**
     * Return the headers of a call made to the uri given.
     */
    private static function headDefinition($uri){
        \App::abort(500, "Method not yet implemented.");
    }

    /*
     * GET an info document based on the uri provided
     */
    private static function getLicenses($uri){

        // Fetch the columns that can be shown in the result
        $columns = \License::getColumns();

        $licenses = array();

        // Translate 0 and 1 to json booleans
        foreach(\License::all($columns) as $license){

            $tmp = array();
            foreach($columns as $column){
                $tmp[$column] = $license->$column;
            }
            array_push($licenses, $tmp);
        }

        return self::makeResponse($licenses);
    }

    /**
     * Return the response with the given data ( formatted in json )
     */
    private static function makeResponse($data){

         // Create response
        $response = \Response::make(str_replace('\/','/', json_encode($data, true)));

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

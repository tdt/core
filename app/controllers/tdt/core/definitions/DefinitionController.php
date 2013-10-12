<?php

namespace tdt\core\definitions;

/**
 * DefinitionController
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DefinitionController extends \Controller {

    public static function handle($uri){

        // Propage the request based on the HTTPMethod of the request.
        $request = \Request::createFromGlobals();
        $method = $request->getRealMethod();
        
        switch($method){
            case "PUT":                
                self::createDefinition();
                break;
            case "GET":
                // TODO return the existing definitions, these should be seen only by authenticated peopless
                break;
            case "PATCH":
                self::patchDefinition();
                break;
            case "DELETE":
                self::deleteDefinition();
                break;
            default:
                \App::abort(400, "The method $method is not supported by the definitions.");
                break;
        }
    }


    /**
     * Create a new definition based on the PUT parameters given and content-type.
     */ 
    private static function createDefinition(){

        // Retrieve the parameters of the PUT requests (either a JSON document or a key=value string).    
        $request = \Request::createFromGlobals();
        $params = \Input::all();
        
        // Retrieve the content type and parse out the definition type.
        $content_type = $request->headers->get('content_type');
        $matches = array();
        
        if(preg_match('/application\/tdt\.(.*)/', $content_type, $matches)){
            $definition = ucfirst($matches[1]) . "Definition";
            // TODO implement further.
            
        }else{
            \App::abort(452, "The content-type provided was not recognized, look at the discovery document for the supported content-types.");
        }
        
        // Check if the definition type is legit.
        // Check if the URI isn't taken already.
        // Validate the provided parameters.    
        // Create the new definition.    

    }

    /**
     * Delete a definition based on the URI given.
     */ 
    private static function deleteDefinition(){

    }

    /**
     * PATCH a definition based on the PATCH parameters and URI.
     */ 
    private static function patchDefinition(){

    }

    /**
     * Check if a string is a JSON string
     */ 
    private static function isJson($string){
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
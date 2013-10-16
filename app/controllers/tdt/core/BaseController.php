<?php

namespace tdt\core;

/**
 * The base controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class BaseController extends \Controller {

    /*
     * Handles all core requests
     */
    public function handleRequest($uri){

        // Check first segment of the request
        $segment = \Request::segment(1);

        // Split for an (optional) extension
        preg_match('/([^\.]*)(?:\.(.*))?$/', $segment, $matches);

        // URIsegment is always the first match
        $urisegment = $matches[1];

        switch($urisegment){
            case 'discover':
                $controller = 'tdt\core\definitions\DiscoveryController';
                break;
            // TODO: don't hardcode this part
            case 'definitions':
                $controller = 'tdt\core\definitions\DefinitionController';
                $uri = str_replace('definitions/', '', $uri);
                break;
            default:
                // None of the above -> must be a dataset request
                $controller = 'tdt\core\datasets\DatasetController';
                break;
        }

        return $controller::handle($uri);
    }

}
<?php

namespace Tdt\Core\Definitions;

use Illuminate\Routing\Router;
use Tdt\Core\Auth\Auth;
use Tdt\Core\Datasets\Data;
use Tdt\Core\ContentNegotiator;
use Tdt\Core\Pager;
use Tdt\Core\ApiController;
use Definition;
use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;

/**
 * InfoController: Controller that handles info requests and returns informational data about the datatank.
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class KeywordController extends ApiController
{
    public function __construct(DefinitionRepositoryInterface $definitions)
    {
        $this->definitions = $definitions;
    }

    public function get($uri)
    {
        // Set permission
        Auth::requirePermissions('info.view');

        return $this->getKeywords();
    }

    /**
     * Return the headers of a call made to the uri given.
     */
    public function head($uri)
    {
        $response =  \Response::make(null, 200);

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');
        $response->header('Pragma', 'public');

        // Return formatted response
        return $response;
    }

    /*
     * GET an info document based on the uri provided
     */
    private function getKeywords($uri = null)
    {
        $definitions = $this->definitions->getAll();

        $keywords = [];

        foreach ($definitions as $definition) {
            $keyword_str = $definition['keywords'];

            $keywords_set = explode(',', $keyword_str);

            foreach ($keywords_set as $keyword) {
                $keyword = trim($keyword);

                if (!in_array($keyword, $keywords) && !empty($keyword)) {
                    $keywords[] = $keyword;
                }
            }
        }

        $result = new Data();
        $result->data = $keywords;

        return ContentNegotiator::getResponse($result, 'json');
    }

    /**
     * Return the response with the given data (formatted in json)
     */
    private function makeResponse($data)
    {
         // Create response
        $response = \Response::make(str_replace('\/', '/', json_encode($data)));

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}

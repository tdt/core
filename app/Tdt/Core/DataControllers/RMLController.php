<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Cache\Cache;
use Tdt\Core\Datasets\Data;
use Symfony\Component\HttpFoundation\Request;

/**
 * RML Controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class RMLController extends ADataController
{

    public function readData($source_definition, $rest_parameters = array())
    {

        $uri = $source_definition['mapping_document'];

        $data_result = new Data();
        $data_result->data = ['mapping_document' => $uri];
        $data_result->preferred_formats = $this->getPreferredFormats();

        return $data_result;
    }
}

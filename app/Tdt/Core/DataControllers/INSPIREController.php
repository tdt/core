<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Cache\Cache;
use Tdt\Core\Datasets\Data;
use Symfony\Component\HttpFoundation\Request;
use Tdt\Core\utils\XMLSerializer;

/**
 * Inspire Controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class INSPIREController extends ADataController
{
    public static function getParameters()
    {
        return [];
    }

    public function readData($source_definition, $rest_parameters = array())
    {
        $data_result = new Data();
        $data_result->data = $source_definition;
        $data_result->preferred_formats = $this->getPreferredFormats();

        return $data_result;
    }

    /**
     * Provide an array a formatter priorities
     */
    protected function getPreferredFormats()
    {
        // Both semantic and raw data structures support json
        return ['html'];
    }
}

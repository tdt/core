<?php

/**
 * This class represents the RESTful lookup of our API
 *
 * @copyright (C) 2011, 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\filters;

class RESTFilter extends AFilter {

    public function __construct($params) {
        parent::__construct($params);
    }

    public function filter($result) {
        // we have to store the subresources for logging purposes;
        $subresources = array();

        foreach ($this->params as $resource) {
            if (is_object($result) && isset($result->$resource)) {
                $result = $result->$resource;
            } elseif (is_array($result) && isset($result[$resource])) {
                $result = $result[$resource];
            } else {
                array_push($subresources, $resource);
                $invalidObject = new \stdClass();
                $invalidObject->subresources = $subresources;
                $invalidObject->result = new \stdClass();
                return $invalidObject;
            }
            array_push($subresources, $resource);
        }

        $resultset = new \stdClass();
        $resultset->result = $result;
        $resultset->subresources = $subresources;

        return $resultset;
    }

}

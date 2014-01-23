<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Top class of all filters
 */
abstract class UniversalFilterNode {

    private $type;
    private $attachments;

    public function __construct($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function attach($id, $data) {
        $this->attachments[$id] = $data;
    }

    public function getAttachment($id) {
        return $this->attachments[$id];
    }

}


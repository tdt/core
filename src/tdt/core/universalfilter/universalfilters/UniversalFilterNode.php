<?php

namespace tdt\core\universalfilter\Universalfilters;

use tdt\core\universalfilter\universalfilters\CheckInFunction;
use tdt\core\universalfilter\universalfilters\Identifier;
use tdt\core\universalfilter\universalfilters\NormalFilterNode;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

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


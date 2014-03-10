<?php

namespace Tdt\Core\Spectql\implementation\Universalfilters;

use Tdt\Core\Spectql\implementation\universalfilters\CheckInFunction;
use Tdt\Core\Spectql\implementation\universalfilters\Identifier;
use Tdt\Core\Spectql\implementation\universalfilters\NormalFilterNode;
use Tdt\Core\Spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * Top class of all filters
 */
abstract class UniversalFilterNode
{

    private $type;
    private $attachments;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function attach($id, $data)
    {
        $this->attachments[$id] = $data;
    }

    public function getAttachment($id)
    {
        return $this->attachments[$id];
    }
}

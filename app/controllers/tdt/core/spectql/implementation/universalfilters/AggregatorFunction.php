<?php

namespace tdt\core\spectql\implementation\Universalfilters;

use tdt\core\spectql\implementation\universalfilters\CheckInFunction;
use tdt\core\spectql\implementation\universalfilters\Identifier;
use tdt\core\spectql\implementation\universalfilters\NormalFilterNode;
use tdt\core\spectql\implementation\universalfilters\UniversalFilterNode;

/**
 * This class represents all aggregator functions
 *
 * type: Column -> Cell
 */
class AggregatorFunction extends NormalFilterNode
{

    public static $AGGREGATOR_AVG = "AGGREGATOR_AVG";
    public static $AGGREGATOR_COUNT = "AGGREGATOR_COUNT";
    public static $AGGREGATOR_FIRST = "AGGREGATOR_FIRST";
    public static $AGGREGATOR_LAST = "AGGREGATOR_LAST";
    public static $AGGREGATOR_MAX = "AGGREGATOR_MAX";
    public static $AGGREGATOR_MIN = "AGGREGATOR_MIN";
    public static $AGGREGATOR_SUM = "AGGREGATOR_SUM";

    public function __construct($kind, UniversalFilterNode $column = null) {

        parent::__construct($kind);

        if ($column != null)
            $this->setSource($column);
    }

}

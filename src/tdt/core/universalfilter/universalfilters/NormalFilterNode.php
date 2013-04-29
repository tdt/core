<?php

namespace tdt\core\universalfilter\Universalfilters;

use tdt\core\universalfilter\universalfilters\CheckInFunction;
use tdt\core\universalfilter\universalfilters\Identifier;
use tdt\core\universalfilter\universalfilters\NormalFilterNode;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;

/**
 * *Top class* of all real filters
 * -> all these filters have one or more sources
 *
 * Some filters like joins or binary functions have more than one source.
 */
abstract class NormalFilterNode extends UniversalFilterNode {

    private $source = array(); //of UniversalFilterNode

    /**
     * Is this index a correct index of a source?
     * @param int $index
     */

    private function checkBounds($index) {
        if ($index < 0 || $index >= $this->getSourceCount()) {
            throw new Exception("That is not a valid source-index for this kind of node (node kind: " . get_class($this) . ", index: " . $index . ")");
        }
    }

    /**
     * Sets a source on this NormalFilterNode
     *
     * @param UniversalFilterNode $source
     * @param int $index The index of the source to set. Default: source 0.
     */
    public function setSource(UniversalFilterNode $source, $index = 0) {
        $this->checkBounds($index);
        $this->source[$index] = $source;
    }

    /**
     * Gets a source of this NormalFilterNode
     * @param int $index
     * @return NormalFilterNode the sourcefilter
     */
    public function getSource($index = 0) {
        $this->checkBounds($index);
        if (isset($this->source[$index])) {
            return $this->source[$index];
        } else {
            return null;
        }
    }

    /**
     * How many sources does this filter have? (Most of the time: 1)
     * @return int
     */
    public function getSourceCount() {
        return 1;
    }

}


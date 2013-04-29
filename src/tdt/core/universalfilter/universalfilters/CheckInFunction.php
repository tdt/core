<?php
namespace tdt\core\universalfilter\universalfilters;

use tdt\core\universalfilter\universalfilters\Identifier;
use tdt\core\universalfilter\universalfilters\NormalFilterNode;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;
/**
 * Checks if the value is in a list of constants
 * type: [Cell, [Constant, ...]] -> Cell
 * type: [Column, [Constant, ...]] -> Column
 */
class CheckInFunction extends NormalFilterNode {

    private $constants;
    public static $FUNCTION_IN_LIST = "FUNCTION_IN_LIST"; // is a varargs function

    public function __construct(array /* of Constant */ $constants, UniversalFilterNode $source = null) {
        parent::__construct(CheckInFunction::$FUNCTION_IN_LIST);
        $this->constants = $constants;
        if ($column != null)
            $this->setSource($source);
    }

    public function getConstants() {
        return $this->constants;
    }

}
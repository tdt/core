<?php

/**
 * This is a filternode that does nothing. 
 * It is not a valid filter. You can not execute it.
 * 
 * It can be used as a root of the query-tree, 
 *  so that all real filters have a parent.
 *
 * @package The-Datatank/universalfilter/interpreter/sourceusage
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class DummyUniversalFilterNode extends NormalFilterNode {
    public function __construct(UniversalFilterNode $source=null) {
        parent::__construct("DUMMY");
        if($source!=null) $this->setSource ($source);
    }
}

?>

<?php

/**
 * An object of this class is attached to each UniversalFilterNode in the query to be able to execute it externally.
 *
 * @package The-Datatank/universalfilter
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class ExpectedHeaderNamesAttachment {
    public static $ATTACHMENTID="ExpectedHeaderNamesInfoAttachmentId";
    
    private $expectedheadernames;
    
    public function __construct($expectedheadernames) {
        $this->expectedheadernames=$expectedheadernames;
    }
    
    public function getExpectedHeaderNames(){
        return $this->expectedheadernames;
    }
}

?>

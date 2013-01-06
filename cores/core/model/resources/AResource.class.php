<?php
/**
 * This is an abstract class that needs to be implemented by any installed resource implementation
 *
 * Adapter design pattern
 * 
 * @package The-Datatank/resources
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

abstract class AResource extends AReader{

    public function read(){
        return $this->call();
    }
}
?>

<?php
/**
 * Tools for the SPECTQL parser
 *
 * @package The-Datatank/controllers/spectql
 * @copyright (C) 2011 by OKFN chapter Belgium vzw/asbl
 * @license LGPL
 * @author Pieter Colpaert
 * @organisation Hogent
 */

class SPECTQLTools{
    

    /**
     * Adds columns to a relation
     * @param $relation1 passed by reference. The main relation
     * @param $relation2 passed by reference. All the 
     */
    static public function catRelation(&$relation1, &$relation2){
        for($i = 0; $i < min(array(sizeof($relation1),sizeof($relation2)));$i++){
            $relation1[$i] = array_merge($relation1[$i],$relation2[$i]);
        }
    }
    


}


?>
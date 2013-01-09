<?php
/**
 * Some "hashing" functions
 *
 * @package The-Datatank/universalfilter/common
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */


/**
 * Returns a new string with each character as hexadecimal notation.
 * 
 * @param string $string
 * @return string 
 */
function strToHex($string) {
    $hex='';
    for ($i=0; $i < strlen($string); $i++) {
        $hex .= dechex(ord($string[$i]));
    }
    // Possible bug : this used to return just $hex, no substring, resulting in names that are longer than 265 characters! That's not allowed in most 
    // filesystems (such as NTFS). So we're taking a substring of this.
    return substr($hex,0,150);
}

/**
 * Returns a unique hash for the given string THAT does not contain special characters (only A-Za-z0-9)
 * 
 * Current implementation uses strToHex...
 * 
 * @param string $string
 * @return string 
 */
function hashWithNoSpecialChars($string) {
    return strToHex($string);
}
?>

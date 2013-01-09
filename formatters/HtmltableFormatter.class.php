<?php
/**
 * This file contains the HTML Table printer.
 * 
 * I wrote this file based upon the csv formatter...
 * 
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
namespace tdt\core\formatters;
/**
 * This class inherits from the abstract Formatter. It will return our resultobject into a
 * html table datastructure.
 */

class HtmltableFormatter extends tdt\core\formatters\AFormatter {
    
    private $SHOWNULLVALUES=true;/* show null values as "unknown" or not? If not, you can not see the difference between "null" and "" */


    public function __construct($rootname, $objectToPrint) {
        parent::__construct($rootname, $objectToPrint);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        echo "<html>\n".
             "  <head>\n".
             "    <title>Table Formatter</title>\n".
             "    <style>\n".
             "      #the-table { ".
             "          border:1px solid #bbb;".
             "          border-collapse:collapse; ".
             "      }".
             "      #the-table td,#the-table th { ".
             "          border:1px solid #ccc;".
             "          border-collapse:collapse;".
             "          padding:5px; ".
             "      }".
             "    </style>\n".
             "  </head>\n";        
    }

    /**
     * encloses the $element in double quotes
     */
    private function escape($element) {
        return htmlspecialchars($element);
    }

    public function printBody() {
        
        $keys = array_keys(get_object_vars($this->objectToPrint));
        $key = $keys[0];
        $this->objectToPrint = $this->objectToPrint->$key;

        if (!is_array($this->objectToPrint)) {
            throw new tdt\framework\TDTException(500,array("HTMLFormatter - You can only request HTML-table on an array" , array("CSV", "json", "rdf", "xml", "n3","ttl")));
        }
	$firstrow = reset($this->objectToPrint);
        if($firstrow===FALSE) {
            // NO DATA
            echo "  <body>\n".
                 "    <p><strong>There is no data to display...</strong></p>\n".
                 "  </body>\n".
                 "</html>\n";
            return;
        }
        if (isset($firstrow)) {
            //print the header row
            $headerrow = array();
            if (is_object($firstrow)) {
                $headerrow = array_keys(get_object_vars($firstrow));
            } else {
                $headerrow = array_keys($firstrow);
            }

            // we're going to escape all of our fields
            $enclosedHeaderrow = array();

            foreach ($headerrow as $element) {
                array_push($enclosedHeaderrow, $this->escape($element));
            }
            
            echo "  <body>\n".
                 "\n".
                 "    <table id='the-table'>\n".
                 "      <thead><tr>\n".
                 "        <th>".implode("</th>\n        <th>", $enclosedHeaderrow)."</th>\n".
                 "      </tr></thead>\n<tbody>";

            foreach ($this->objectToPrint as $row) {
                echo "      <tr>\n";
                if (is_object($row)) {
                    $row = get_object_vars($row);
                }

                foreach ($row as $element) {
                    echo "        <td>";
                    if (is_object($element)) {
                        if (isset($element->id)) {
                            echo $element->id;
                        } else if (isset($element->name)) {
                            echo $element->name;
                        } else {
                            echo "OBJECT";
                        }
                    } elseif (is_array($element)) {
                        if (isset($element["id"])) {
                            echo $element["id"];
                        } else if (isset($element["name"])) {
                            echo $element["name"];
                        } else {
                            echo "ARRAY";
                        }
                    } else {
                        if($this->SHOWNULLVALUES && is_null($element)){
                            echo "<i>unknown</i>";
                        }else{
                            echo $this->escape($element);
                        }
                    }
                    echo " </td>\n";
                }
                echo "      </tr>\n";
            }
            echo "    </tbody></table>\n".
                 "\n".
                 "  </body>\n".
                 "</html>";
        }
    }

    public static function getDocumentation() {
        return "A Html Table formater, works only on arrays...";
    }

}
?>

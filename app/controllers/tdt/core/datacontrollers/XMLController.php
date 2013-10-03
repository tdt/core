<?php

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;
use Symfony\Component\HttpFoundation\Request;
use tdt\core\utils\XMLSerializer;

/**
* XML Controller
* @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
* @license AGPLv3
* @author Jan Vansteenlandt <jan@okfn.be>
* @author Michiel Vancoillie <michiel@okfn.be>
*/
class XMLController implements IDataController {

    public function readData($source_definition, $parameters = null){

        $xml_ser = $this->xmlstr_to_array(file_get_contents($source_definition->uri));

        $data_result = new Data();
        $data_result->data = $xml_ser;

        return $data_result;
    }

    /**
     * Initialize recursion.
     */
    private function xmlstr_to_array($xmlstr) {
        $doc = new \DOMDocument();
        $doc->loadXML($xmlstr);
        return $this->domnode_to_array($doc->documentElement);
    }

    /**
     * Convert node to a PHP array.
     */
    private function domnode_to_array($node) {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;

            case XML_ELEMENT_NODE:
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domnode_to_array($child);
                    if(isset($child->tagName)) {
                        $t = $child->tagName;
                        if(!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    }
                    elseif($v) {
                        $output = (string) $v;
                    }
                }

                if(is_array($output)) {
                    if($node->attributes->length) {
                        $a = array();
                        foreach($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if(is_array($v) && count($v)==1 && $t!='@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }else{
                    $value = $output;
                    $output = array();
                    if($node->attributes->length) {
                        $a = array();
                        foreach($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }

                    $output['@value'] = $value;

                }

                break;
        }
        return $output;
    }
}
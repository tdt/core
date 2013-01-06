<?php
 /**
   * This file serializes an xml file to a php object
   * 
   * @package The-DataTank/includes
   */

Class XmlUtils {

    public static function xmlToObject($xml, $obj = null) {
        if (!$obj) $obj = new StdClass();
        //**********************************************************
        // Create array of unique node names
        $uniqueNodeNames = array();
        foreach ($xml->children() as $xmlChild){
            if(!isset($uniqueNodeNames[$xmlChild->getName()])){
                $uniqueNodeNames[$xmlChild->getName()] = 0;
            }
            @$uniqueNodeNames[$xmlChild->getName()]++;
        }
        //**********************************************************
        // Create child types - object for single nodes, array of objects for multi nodes:
        foreach ($uniqueNodeNames as $nodeName => $nodeCount) {
            if ($nodeCount > 1) {
                $obj->$nodeName = array();
                for ($i=0; $i<$nodeCount; $i++) {
                    array_push($obj->$nodeName, new StdClass());
                }
            } else {
                $obj->$nodeName = new StdClass();
            }
        }
        //**********************************************************
        // For each child node: add attributes as object properties and invoke recursion
        $arrayIdx = array();
        
        
        foreach ($xml->children() as $xmlChild) {
            $str = trim($xmlChild);
            //print_r($xmlChild->attributes());
            $nodeText = trim($xmlChild);
            $nodeName = $xmlChild->getName();
            // If child is array
            if (is_array($obj->$nodeName)) {
                if(!isset($arrayIdx[$nodeName])){
                    $arrayIdx[$nodeName] = 0;
                }
                
                $idx = (int)@$arrayIdx[$nodeName];
                $objArray = $obj->$nodeName;
                // Add attributes as object properties
                foreach($xmlChild->attributes() as $attributeType => $attributeValue) {     
                    $attributeType = "_" . $attributeType;
                    $objArray[$idx]->$attributeType = (string)$attributeValue;
                }
                // If element text (e.g. <node>ElementText<node>
                if (strlen($nodeText)) $objArray[$idx]->$nodeName = $nodeText;
                // Invoke recursion
                XmlUtils::xmlToObject($xmlChild, $objArray[$idx]);
            }
            // If child is object
            if (is_object($obj->$nodeName)) {
                // Add attributes as object properties
                foreach($xmlChild->attributes() as $attributeType => $attributeValue) {
                    $attributeType = "_" . $attributeType;
                    $obj->$nodeName->$attributeType = (string)$attributeValue;
                }
                // If element text (e.g. <node>ElementText<node>
                if (strlen($nodeText)) $obj->$nodeName = $nodeText;
                // Invoke recursion
                XmlUtils::xmlToObject($xmlChild, $obj->$nodeName);
            }
            if(!isset($arrayIdx[$nodeName])){
                $arrayIdx[$nodeName] = 0;
            }
            
            @$arrayIdx[$nodeName]++;
        }
        return $obj;
    }

    public static function xmlFileToObject($xmlFileName) {
        //if (!file_exists($xmlFileName)) die ("XmlUtils::xmlFileToObject Error: $xmlFileName nonexistent!");
        $xml = simplexml_load_file($xmlFileName);
        return XmlUtils::xmlToObject($xml);
    }
}
?>
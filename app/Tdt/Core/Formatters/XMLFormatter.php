<?php

namespace Tdt\Core\Formatters;


define("NUMBER_TAG_PREFIX", "_");
define("DEFAULT_ROOTNAME", "data");

/**
 * XML Formatter
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class XMLFormatter implements IFormatter
{

    public static function createResponse($dataObj)
    {

        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'text/xml;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj)
    {

        // Rootname equals resource name
        $rootname = 'root';

        // Check for semantic source
        if ($dataObj->is_semantic) {

            // Check if a configuration is given
            $conf = array();
            if (!empty($dataObj->semantic->conf)) {
                $conf = $dataObj->semantic->conf;
            }

            return $dataObj->data->serialise('rdfxml');
        }

        // Build the body
        $body = '<?xml version="1.0" encoding="UTF-8" ?>';
        $body .= self::transformToXML($dataObj->data, $rootname);

        return $body;
    }

    private static function transformToXML($data, $nameobject)
    {

        // Set the tagname
        $xml_tag = str_replace(' ', '_', $nameobject);

        // Start an empty object to add to the document
        $object = '';

        if (self::isAssociative($data)) {

            $object = "<$xml_tag>";

            // Check for attributes
            if (!empty($data['@attributes'])) {

                $attributes = $data['@attributes'];

                if (is_array($attributes) && count($attributes) > 0) {
                    // Trim last '>'
                    $object = rtrim($object, '>');

                    // Add attributes
                    foreach ($attributes as $name => $value) {
                        $object .= " $name='".$value."'";
                    }

                    $object .= '>';
                }
            }

            unset($data['@attributes']);

            // Data is an array (translates to elements)
            foreach ($data as $key => $value) {

                // Check for special keys, then add elements recursively
                if ($key === '@value') {
                    $object .= self::getXMLString($value);
                } elseif ($key == '@attributes') {
                    $object .= self::transformToXML($value, 'attributes');
                } elseif (is_numeric($key)) {
                    $object .= self::transformToXML($value, 'key');
                } elseif ($key == '@text') {
                    $object .= $value;
                } else {
                    $object .= self::transformToXML($value, $key);
                }

            }

            // Close tag
            $object .= "</$xml_tag>";
        } elseif (is_object($data)) {

            $object = "<$xml_tag>";

            // Data is object
            foreach ($data as $key => $value) {
                // Recursively add elements
                $object .= self::transformToXML($value, $key);
            }

            // Close tag
            $object .= "</$xml_tag>";

        } elseif (is_array($data)) {

            // We have a list of elements
            foreach ($data as $element) {

                $object .= self::transformToXML($element, $xml_tag);
            }

        } else {

            // Data is string append it
            $object .= self::getXMLString($data);
        }



        return $object;
    }

    private static function getXMLString($string)
    {
        // Check for XML syntax to escape
        if (preg_match('/[<>&]+/', $string)) {
            $string = '<![CDATA[' . $string . ']]>';
        }

        return $string;
    }

    private static function isAssociative($arr)
    {
        return (bool)count(array_filter(array_keys($arr), 'is_string'));
    }

    public static function getDocumentation()
    {
        return "Prints plain old XML. Watch out for tags starting with an integer: an underscore will be added.";
    }
}

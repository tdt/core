<?php

namespace Tdt\Core\Formatters;

define("NUMBER_TAG_PREFIX", "_");
define("DEFAULT_ROOTNAME", "data");

/**
 * XML Formatter
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class XMLFormatter implements IFormatter
{

    public static $prefixes = array();
    public static $isRootElement = true;

    public static function createResponse($dataObj)
    {
        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers

        if ($dataObj->is_semantic) {
            // The response contains rdf+xml
            $response->header('Content-Type', 'application/rdf+xml;charset=UTF-8');
        } else {
            $response->header('Content-Type', 'text/xml;charset=UTF-8');
        }

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

        self::$prefixes = $dataObj->semantic;

        $body .= self::transformToXML($dataObj->data, $rootname);

        return $body;
    }

    /**
     * Return a valid xml tag name, use URI prefixes if necessary
     *
     * @param string $name
     *
     * @return string
     */
    private static function getFullName($name)
    {
        // If the name contains a http:// then it should be prefixed

        if (strpos($name, '://')) {

            foreach (self::$prefixes as $prefix => $uri) {

                $prefixedName = str_replace($uri, $prefix . ':', $name);

                if ($prefixedName != $name) {
                    return $prefixedName;
                }
            }
        }

        return $name;
    }

    private static function transformToXML($data, $nameobject)
    {
        // Set the tagname, replace whitespaces with an underscore
        $xml_tag = str_replace(' ', '_', $nameobject);

        $xml_tag = self::getFullName($xml_tag);

        // Start an empty object to add to the document
        $object = '';

        if (is_array($data) && self::isAssociative($data)) {

            $object = "<$xml_tag";

            if (self::$isRootElement) {

                self::$isRootElement = false;

                foreach (self::$prefixes as $prefix => $uri) {
                    $object .= " xmlns:" . $prefix . "=\"$uri\"";
                }
            }

            $object .=">";

            // Check for attributes
            if (!empty($data['@attributes'])) {

                $attributes = $data['@attributes'];

                if (is_array($attributes) && count($attributes) > 0) {
                    // Trim last '>'
                    $object = rtrim($object, '>');

                    // Add attributes
                    foreach ($attributes as $name => $value) {

                        $name = self::getFullName($name);

                        $object .= " " . $name . '=' . '"' . htmlspecialchars($value) . "'";
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
                } elseif (is_numeric($key)) {
                    $object .= self::transformToXML($value, 'i' . $key);
                } elseif ($key == '@text') {
                    if (is_array($value)) {
                        $object .= implode(' ', $value);
                    } else {
                        // Replace XML entities: < > & ' "
                        $object .= htmlspecialchars($value);
                    }
                } else {
                    $object .= self::transformToXML($value, $key);
                }
            }

            // Close tag
            $object .= "</$xml_tag>";

        } elseif (is_object($data)) {

            $object .= "<$xml_tag>";

            $data = get_object_vars($data);

            // Data is object
            foreach ($data as $key => $element) {
                if (is_numeric($key)) {
                    $object .= self::transformToXML($element, 'i' . $key);
                } else {
                    $object .= self::transformToXML($element, $key);
                }
            }

            // Close tag
            $object .= "</$xml_tag>";

        } elseif (is_array($data)) {

            $object .= "<$xml_tag>";

            // We have a list of elements
            foreach ($data as $key => $element) {

                if (is_numeric($key)) {
                    $object .= self::transformToXML($element, 'element');
                } else {
                    $object .= self::transformToXML($element, $key);
                }
            }

            $object .= "</$xml_tag>";

        } else {

            $object .= "<$xml_tag>";

            // Data is string append it
            $object .= self::getXMLString($data);
            $object .= "</$xml_tag>";
        }



        return $object;
    }

    private static function getXMLString($string)
    {
        // Check for XML syntax to escape
        if (preg_match('/[<>&]+/', $string)) {
            $string = '<![CDATA[' . $string . ']]>';
        }

        // Replace XML entities: < > & ' "

        return htmlspecialchars($string);
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

<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Cache\Cache;
use Tdt\Core\Datasets\Data;
use Symfony\Component\HttpFoundation\Request;
use Tdt\Core\utils\XMLSerializer;

/**
 * XML Controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class XMLController extends ADataController
{

    public function readData($source_definition, $rest_parameters = array())
    {

        $uri = $source_definition['uri'];

        // Keep track of the prefix URI's
        $this->prefixes = array();

        // Check for caching
        if (Cache::has($uri)) {
            $data = Cache::get($uri);
        } else {

            // Fetch the data
            $data =@ file_get_contents($uri);

            if (!empty($data)) {
                $data = $this->XMLStringToArray($data);
                Cache::put($uri, $data, $source_definition['cache']);
            } else {
                $uri = $source_definition['uri'];
                \App::abort(500, "Cannot retrieve data from the XML file located on $uri.");
            }
        }

        $data_result = new Data();
        $data_result->data = $data;
        $data_result->semantic = $this->prefixes;
        $data_result->preferred_formats = $this->getPreferredFormats();

        return $data_result;
    }

    /**
     * Initialize recursion.
     */
    private function XMLStringToArray($xmlstr)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xmlstr);

        return $this->convertDomNodeToArray($doc->documentElement);
    }

    /**
     * Get the full name of an element or attribute, namespace + name
     *
     * @param DOMElement $element (either DomAttr or DOMElement)
     * @param boolean    $isAttribute
     *
     * @return string
     */
    private function getFullName($element, $isAttribute = false)
    {
        $prefix = $element->prefix;

        if (!empty($prefix)) {

            // Register the namespace and prefix
            $this->prefixes[$prefix] = $element->namespaceURI;

            if ($isAttribute) {

                $attrName = $element->name;

                return $element->namespaceURI . $attrName;
            } else {

                $tagName = $element->tagName;

                return str_replace($prefix . ':', $element->namespaceURI, $tagName);
            }
        } else {
            if (!empty($element->tagName)) {
                return $element->tagName;
            } else {
                return $element->name;
            }

        }
    }

    /**
     * Convert node to a PHP array.
     */
    private function convertDomNodeToArray($node)
    {
        $output = array();

        switch ($node->nodeType) {

            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:

                $output = trim($node->textContent);
                break;

            case XML_ELEMENT_NODE:

                // Check children
                for ($i = 0; $i < $node->childNodes->length; $i++) {

                    // Get child
                    $child = $node->childNodes->item($i);

                    // Recursive fetch child XML
                    $value = $this->convertDomNodeToArray($child);

                    // Check if child is a tag
                    if (isset($child->tagName)) {

                        // Current tag
                        $tag = $this->getFullName($child);

                        // Check if current tag is already defined
                        if (!isset($output[$tag])) {

                            // If not, inititialize array
                            $output[$tag] = array();
                        }

                        // Push the child tag on the array
                        $output[$tag][] = $value;

                    } elseif ($value) {

                        // Child is plain text, preliminary solution
                        if (empty($output['@text'])) {
                            $output['@text'] = array();
                        }

                        array_push($output['@text'], (string) $value);
                    }
                }

                // Element is not a text node
                if (is_array($output)) {

                    // Check if element has attributes
                    $attributesLength = $node->attributes->length;

                    if ($attributesLength > 0) {

                        $attributes = array();

                        for ($i = 0; $i < $attributesLength; $i++) {

                            $attribute = $node->attributes->item($i);

                            $attributeName = $this->getFullName($attribute, true);
                            $attributes[$attributeName] = (string) $attribute->value;
                        }

                        if (!empty($attributes)) {
                            $output['@attributes'] = $attributes;
                        }

                    }
                    // For each of the element's children
                    foreach ($output as $tag => $value) {

                        if (is_array($value) && count($value) == 1 && $tag != '@attributes') {
                            $output[$tag] = @$value[0];
                        }
                    }
                } else {
                    // Element is a text node, but can still have attributes
                    $value = $output;

                    // Check if element has attributes
                    $attributesLength = $node->attributes->length;
                    if ($attributesLength > 0) {

                        $attributes = array();

                        for ($i = 0; $i < $attributesLength; $i++) {

                            $attribute = $node->attributes->item($i);

                            $attributeName = $this->getFullName($attribute, true);
                            $attributes[$attributeName] = (string) $attribute->value;
                        }

                        if (!empty($attributes)) {
                            $output['@attributes'] = $attributes;
                        }
                    }

                    array_push($output['@text'], $value);

                }
                break;
        }

        return $output;
    }
}

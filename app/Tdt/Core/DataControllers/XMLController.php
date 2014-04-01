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
                        $tag = $child->tagName;

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
                    if ($node->attributes->length > 0) {

                        $attributes = array();

                        foreach ($node->attributes as $name => $attr) {
                            $attributes[$name] = (string) $attr->value;
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
                    //$output = array();

                    // Check if element has attributes
                    if ($node->attributes->length > 0) {

                        $attributes = array();

                        foreach ($node->attributes as $name => $attr) {
                            $attributes[$name] = (string) $attr->value;
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

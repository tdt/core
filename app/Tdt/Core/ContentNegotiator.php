<?php

namespace Tdt\Core;

use Tdt\Core\Formatters\FormatHelper;
use Negotiation\FormatNegotiator;

/**
 * Content Negotiator
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class ContentNegotiator extends Pager
{

    /**
     * Map MIME-types on formatters for Accept-header
     */
    public static $mime_types_map = array(
        'text/csv' => 'CSV',
        'text/html' => 'HTML',
        'application/json' => 'JSON',
        'application/xml' => 'XML',
        'application/xslt+xml' => 'XML',
        'application/xhtml+xml' => 'HTML',
        'text/turtle' => 'ttl',
        'application/n-triples' => 'nt',
        'application/ld+json' => 'jsonld',
    );

    /**
     * Format using requested formatter (via extension, Accept-header or default)
     *
     * @param Tdt\Core\Datasets\Data $data      The data object on which the response will be based
     * @param string                 $extension The preferred format in which the data should be returned
     *
     * @return \Response
     */
    public static function getResponse($data, $extension = null)
    {
        // Check Accept-header
        $accept_header = \Request::header('Accept');

        // Safety first
        $extension = strtoupper($extension);

        // Formatter class
        $formatter_class = 'Tdt\\Core\\Formatters\\' . $extension . 'Formatter';

        if (!class_exists($formatter_class)) {

            $negotiator = new FormatNegotiator();

            foreach (self::$mime_types_map as $mime => $format_name) {
                $negotiator->registerFormat($format_name, array($mime), true);
            }

            // Add our own priorities, based on the type of data
            $priorities = array('*/*');

            if (empty($data->is_semantic) && !$data->is_semantic) {
                // Default formatter for non semantic data
                array_push($priorities, 'json');
            } else {
                array_push($priorities, 'ttl');
            }

            // Always head back to the html formatter as a last priority
            array_push($priorities, 'html');

            $format = $negotiator->getBestFormat($accept_header, $priorities);

            if (empty($format)) {

                $format_helper = new FormatHelper();

                $available_formats = implode(', ', array_values($format_helper->getAvailableFormats($data)));

                \App::abort(406, "The requested Content-Type is not supported, the supported formats for this resource are: " . $available_formats);
            }

            // Safety first
            $extension = strtoupper($format);

            // Formatter class
            $formatter_class = 'Tdt\\Core\\Formatters\\' . $extension . 'Formatter';
        }


        // Create the response from the designated formatter
        $response = $formatter_class::createResponse($data);

        // Set the paging header
        if (!empty($data->paging)) {
            $response->header('Link', self::getLinkHeader($data->paging));
        }

        // Set the URI template header
        if (!empty($data->optional_parameters) || !empty($data->rest_parameters)) {

            // http://www.mnot.net/blog/2006/10/04/uri_templating
            $link_template = self::fetchUrl($extension);

            if (substr($link_template, -1, 1) == '/') {
                $link_template = substr($link_template, 0, -1);
            }

            // Add the required parameters
            foreach ($data->rest_parameters as $required_parameter) {
                $link_template .= '/{' . $required_parameter . '}';
            }

            // Add the extension if given
            if (!empty($extension)) {
                $link_template .= '.' . strtolower($extension);
            }

            // Add the optional parameters
            if (!empty($data->optional_parameters)) {

                $link_template .= '{?';

                foreach ($data->optional_parameters as $optional_parameter) {
                    $link_template .= $optional_parameter . ', ';
                }

                $link_template = rtrim($link_template, ', ');
                $link_template .= '}';
            }

            $response->header('Link-Template', $link_template);
        }

        // Cache settings
        $cache_minutes = -1;

        if (\Config::get('cache.enabled', true)) {

            $cache_minutes = 1;

            // Cache per resource
            if (!empty($data->source_definition['cache'])) {
                $cache_minutes = $data->source_definition['cache'];
            }
        }

        // Cache headers
        $response->header('Cache-Control', 'public, max-age='. $cache_minutes*60 .', pre-check='. $cache_minutes*60 .'');
        $response->header('Pragma', 'public');
        $response->header('Expires', date(DATE_RFC822, strtotime("$cache_minutes minute")));

        // Return formatted response
        return $response;
    }

    /**
     * Fetch the url without the extension
     *
     * @param string $extension The extracted extension from the request
     *
     * @return string
     */
    private static function fetchUrl($extension = '')
    {
        $url = \Request::url();

        if (!empty($extension)) {
            $extension = '.' . strtolower($extension);
        }

        $pos = strrpos($url, $extension);

        if ($pos !== false) {
            $url = substr_replace($url, '', $pos, strlen($extension));
        }

        return $url;
    }
}

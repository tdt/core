<?php

namespace Tdt\Core\Formatters;

/**
 * CSV Formatter
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class CSVFormatter implements IFormatter
{
    public static function createResponse($dataObj)
    {
        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'text/csv;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj)
    {
        // Check if its a result of a SPARQL select query
        if ($dataObj->source_definition['type'] == 'SPARQL' && $dataObj->source_definition['query_type'] == 'select') {
            $dataObj->data = self::buildTableFromSparqlResult($dataObj->data);
        }

        if (!is_array($dataObj->data)) {
            \App::abort(400, "You can only request a CSV formatter on a tabular data structure.");
        }

        // Build the body
        $body = '';

        $header_printed = false;
        foreach ($dataObj->data as $row) {
            if (is_object($row)) {
                $row = get_object_vars($row);
            } elseif (!is_array($row)) {
                $body .= $row . "\n";
                continue;
            }

            // Print header
            if (!$header_printed) {
                $i = 0;
                foreach ($row as $key => $value) {
                    $body .= CSVFormatter::enclose($key);
                    $body .= sizeof($row)-1 != $i ? ";" : "\n";
                    $i++;
                }
                $header_printed = true;
            }

            $i = 0;
            foreach ($row as $element) {
                if (is_object($element)) {
                    \App::abort(400, "You can only request a CSV formatter on a tabular datastructure.");
                } elseif (is_array($element)) {
                    \App::abort(400, "You can only request a CSV formatter on a tabular datastructure.");
                } else {
                    $body .= CSVFormatter::enclose($element);
                }
                $body .= sizeof($row)-1 != $i ? ";" : "\n";
                $i++;
            }
        }

        return $body;
    }

    public static function getDocumentation()
    {
        return "A CSV formatter. Works only on tabular data.";
    }


    /**
     * Encloses the $element in double quotes.
     */
    private static function enclose($element)
    {
        $element = rtrim($element, '"');
        $element = ltrim($element, '"');

        // RFC-4180 - 2.7
        $element = str_replace('"', '""', $element);

        $element = '"'.$element.'"';
        return $element;
    }

    /**
     * Build a table from a SPARQL select query result
     *
     * @param mixed semantic_results
     *
     * @return array
     */
    public static function buildTableFromSparqlResult($semantic_results)
    {
        $data = [];

        $header_columns = $semantic_results['head']['vars'];

        foreach ($semantic_results['results']['bindings'] as $row) {
            $row_data = [];

            foreach ($header_columns as $header_column) {
                $row_data[$header_column] = $row[$header_column]['value'];
            }

            $data[] = $row_data;
        }

        return $data;
    }
}

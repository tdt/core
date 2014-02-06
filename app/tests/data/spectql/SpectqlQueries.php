<?php

/**
 * Class containing SPARQL queries that are used in the unittesting.
 *
 * @author Jan Vansteenlandt jan@okfn.be
 */
class SpectqlQueries{

    public static $queries = array(

        'tabular' => array(
            'geo' => array(
                'definition' => array(
                    'type' => 'csv',
                    'delimiter' => ';',
                    'uri' => "/data/csv/geo_csv.csv",
                    'description' => 'csv geo',
                ),
                'queries' => array(
                    array(
                        'query' => "csv/geo{*}?Unit_Type>'District'&Dist_Name>'F':json",
                        'result_count' => 24,
                        'first_row' => '{"lon":"62.23","lat":"32.24","Unit_Type":"Provincial_Center","Dist_Name":"Farah","Prov_Name":"Farah","Dist_ID":"3101","Prov_ID":"31"}',
                    ),
                ),
            ),
        ),
    );

}
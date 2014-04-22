<?php

namespace Tdt\Core\Tests\Data\Spectql;

/**
 * Class containing SpectQL queries for unittesting purposes.
 *
 * @author Jan Vansteenlandt jan@okfn.be
 */
class SpectqlQueries
{

    public static $queries = array(

        'tabular' => array(
            'geo' => array(
                'definition' => array(
                    'type' => 'csv',
                    'delimiter' => ';',
                    'uri' => "/csv/geo_csv.csv",
                    'description' => 'csv geo',
                ),
                'queries' => array(
                    // select all without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{*}:json",
                        'result_count' => 399,
                        'first_result' => '{"lon":"61.33","lat":"32.4","Unit_Type":"District","Dist_Name":"Qala-e-Kah","Prov_Name":"Farah","Dist_ID":"3106","Prov_ID":"31"}',
                    ),
                    // select all with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{*}?Unit_Type>'District'&Dist_Name>'Pusht Rod':json",
                        'result_count' => 117,
                        'first_result' => '{"lon":"61.33","lat":"32.4","Unit_Type":"District","Dist_Name":"Qala-e-Kah","Prov_Name":"Farah","Dist_ID":"3106","Prov_ID":"31"}',
                    ),
                    // select columns with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{lat, Dist_Name}?Unit_Type>'District'&Dist_Name>'Pusht Rod':json",
                        'result_count' => 117,
                        'first_result' => '{"lat":"32.4","Dist_Name":"Qala-e-Kah"}',
                    ),
                    // average without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{avg(lon)}:json",
                        'result_count' => 1,
                        'first_result' => '{"avg_lon":67.793634085213}',
                    ),
                    // average with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{avg(lat)}?Prov_Name=='Uruzgan':json",
                        'result_count' => 1,
                        'first_result' => '{"avg_lat":32.822}',
                    ),
                    // count without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{count(lat)}:json",
                        'result_count' => 1,
                        'first_result' => '{"count_lat":399}',
                    ),
                    // count with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{count(lat)}?Prov_Name=='Uruzgan':json",
                        'result_count' => 1,
                        'first_result' => '{"count_lat":5}',
                    ),
                    // first without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{first(Prov_Name)}:json",
                        'result_count' => 1,
                        'first_result' => '{"first_Prov_Name":"Farah"}',
                    ),
                    // first with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{first(Prov_Name)}?Dist_Name>'F':json",
                        'result_count' => 1,
                        'first_result' => '{"first_Prov_Name":"Farah"}',
                    ),
                    // last without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{last(Prov_Name)}:json",
                        'result_count' => 1,
                        'first_result' => '{"last_Prov_Name":"Balkh"}',
                    ),
                    // last with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{last(Prov_Name)}?Dist_Name>'F':json",
                        'result_count' => 1,
                        'first_result' => '{"last_Prov_Name":"Balkh"}',
                    ),
                    // max without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{max(lat)}:json",
                        'result_count' => 1,
                        'first_result' => '{"max_lat":38.23}',
                    ),
                    // max with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{max(lat)}?Prov_Name=='Uruzgan':json",
                        'result_count' => 1,
                        'first_result' => '{"max_lat":33}',
                    ),
                    // min without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{min(lat)}:json",
                        'result_count' => 1,
                        'first_result' => '{"min_lat":29.88}',
                    ),
                    // min with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{min(lat)}?Prov_Name=='Uruzgan':json",
                        'result_count' => 1,
                        'first_result' => '{"min_lat":32.58}',
                    ),
                    // sum without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{sum(lat)}:json",
                        'result_count' => 1,
                        'first_result' => '{"sum_lat":13799.93}',
                    ),
                    // sum with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{sum(lat)}?Prov_Name=='Uruzgan':json",
                        'result_count' => 1,
                        'first_result' => '{"sum_lat":164.11}',
                    ),
                    // ucase without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{ucase(Prov_Name)}:json",
                        'result_count' => 399,
                        'first_result' => '{"ucase_Prov_Name":"FARAH"}',
                    ),
                    // ucase with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{ucase(Prov_Name)}?Prov_Name=='Uruzgan':json",
                        'result_count' => 5,
                        'first_result' => '{"ucase_Prov_Name":"URUZGAN"}',
                    ),
                    // lcase without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{lcase(Prov_Name)}:json",
                        'result_count' => 399,
                        'first_result' => '{"lcase_Prov_Name":"farah"}',
                    ),
                    // lcase with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{lcase(Prov_Name)}?Prov_Name=='Uruzgan':json",
                        'result_count' => 5,
                        'first_result' => '{"lcase_Prov_Name":"uruzgan"}',
                    ),
                    // len without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{len(Prov_Name)}:json",
                        'result_count' => 399,
                        'first_result' => '{"len_Prov_Name":5}',
                    ),
                    // len with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{len(Prov_Name)}?Prov_Name=='Uruzgan':json",
                        'result_count' => 5,
                        'first_result' => '{"len_Prov_Name":7}',
                    ),
                    // asort without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{asort(Prov_Name),Dist_Name}:json",
                        'result_count' => 399,
                        'first_result' => '{"Prov_Name":"Badakhshan","Dist_Name":"Shahr-e-Buzorg"}',
                    ),
                    // asort with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{asort(Prov_Name),Dist_Name}?Dist_Name<'F':json",
                        'result_count' => 105,
                        'first_result' => '{"Prov_Name":"Badakhshan","Dist_Name":"Darwaz"}',
                    ),
                    // dsort without filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{dsort(Prov_Name),Dist_Name}:json",
                        'result_count' => 399,
                        'first_result' => '{"Prov_Name":"Zabul","Dist_Name":"Tarnak Wa Jaldak"}',
                    ),
                    // dsort with filter
                    array(
                        'query' => "http://localhost/spectql/tabular/geo{dsort(Prov_Name),Dist_Name}?Dist_Name<'F':json",
                        'result_count' => 105,
                        'first_result' => '{"Prov_Name":"Zabul","Dist_Name":"Atghar"}',
                    ),
                ),
            ),
        ),
    );
}

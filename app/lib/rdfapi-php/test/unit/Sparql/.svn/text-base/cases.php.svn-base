<?php
/**
*   Defines Sparql test case data that are evaluated
*   in the sparql unit tests.
*
*   Lets us reuse the tests for multiple engines
*/

$_SESSION['sparqlTestGroups'] = array(
    'dawg'  => array(//'deact'=>1,
        'title'     => 'DAWG tests',
        'tests'     => 'sparql_dawg_tests',
        'checkfunc' => 'resultCheck'
    ),
    'sort'  => array(//'deact'=>1,
        'title'     => 'sorting tests',
        'tests'     => 'sparql_sort_tests',
        'checkfunc' => 'resultCheckSort'
    ),
    'limit' => array(//'deact'=>1,
        'title'     => 'limit/offset tests',
        'tests'     => 'sparql_limitOffset_tests',
        'checkfunc' => 'resultCheckSort'
    ),
    'filter' => array(//'deact'=>1,
        'title'     => 'filter tests',
        'tests'     => 'sparql_filter_tests',
        'checkfunc' => 'resultCheck'
    ),
    'union' => array(//'deact'=>1,
        'title'     => 'custom tests',
        'tests'     => 'sparql_custom_tests',
        'checkfunc' => 'resultCheck'
    ),
    'arq' => array(//'deact'=>1,
        'title'     => 'arq tests',
        'tests'     => 'sparql_arq_tests',
        'checkfunc' => 'resultCheck'
    ),
    'ask'   => array(//'deact'=> 1,
        'title'     => 'ASK and COUNT tests',
        'tests'     => 'sparql_ask_tests',
        'checkfunc' => 'resultCheck'
    ),
    'dawg2'  => array(//'deact'=>1,
        'title'     => 'DAWG2 tests',
        'tests'     => 'sparql_dawg2_tests',
        'checkfunc' => 'resultCheck'
    ),
);

$_SESSION['testSparqlDbTestsIgnores'] = array(
    //hiding optional values that don't match the filter are not possible in sql
    'expr-1',
    //different timezones cannot be used
    'ex11.2.3.1_1',
    //bound() across union patterns is not possible
    'ex11.2.3.2_0',
    //test is broken IMO (cweiske)
    'query-survey-1',

    //fatal error
    'syntax-form-describe01.rq',
    'syntax-form-describe02.rq',
    //parse error
    'dawg-construct-identity'
);



$_SESSION['sparql_dawg_tests'] = array(
     1 => "dawg-tp-01" ,
     2 => "dawg-tp-02" ,
     3 => "dawg-tp-03" ,
     4 => "dawg-tp-04" ,
     5 => "ex2-1a" ,
     6 => "ex2-2a" ,
     7 => "ex2-3a" ,
     8 => "ex2-4a" ,
     9 => "q-opt-1" ,
    10 => "q-opt-2" ,
    11 => "dawg-query-001" ,
    12 => "dawg-query-002" ,
    13 => "dawg-query-003" ,
    14 => "dawg-query-004" ,
    15 => "q-select-1" ,
    16 => "q-select-2" ,
    17 => "q-select-3" ,
    18 => "query-survey-1"
);



$_SESSION['sparql_custom_tests'] = array(
     1  => "customUnion1" ,
     2  => "customUnion2"
);



$_SESSION['sparql_sort_tests'] = array(

     1 => array('data'   => 'data-sort-4.n3',
                'query'  => "query-sort-5",
                'result' => "sort5") ,

     2 => array('data'   => "data-sort-4.n3",
                'query'  => "query-sort-4",
                'result' => "sort4") ,

     3 => array('data'   => "data-sort-3.n3",
                'query'  => "query-sort-3",
                'result' => "sort3") ,

     4 => array('data'   => "data-sort-1.n3",
                'query'  => "query-sort-2",
                'result' => "sort2") ,

     5 => array('data'   => "data-sort-1.n3",
                'query'  => "query-sort-1",
                'result' => "sort1") ,

     6 => array('data'   => "data-sort-6.n3",
                'query'  => "query-sort-6",
                'result' => "sort6") ,
//file is missing
//      7 => array('data'   => "data-sort-7.n3",
//                 'query'  => "query-sort-7",
//                 'result' => "sort7") ,

     8 => array('data'   => "data-sort-8.n3",
                'query'  => "query-sort-4",
                'result' => "sort8") ,

     9 => array('data'   => "data-sort-datetime.n3",
                'query'  => "query-sort-datetime",
                'result' => "sort-datetime") ,
);



$_SESSION['sparql_limitOffset_tests'] = array(
     1 => array('data'   => "data-sort-4.n3",
                'query'  => "LimitOff_1",
                'result' => "LimitOff_1") ,

     2 => array('data'   => "data-sort-4.n3",
                'query'  => "LimitOff_2",
                'result' => "LimitOff_2") ,

     3 => array('data'   => "data-sort-4.n3",
                'query'  => "LimitOff_3",
                'result' => "LimitOff_3") ,
);



$_SESSION['sparql_filter_tests'] = array(
/**/
     1 => array('data'   => "data.n3",
                'query'  => "bound1",
                'result' => "bound1") ,

     2 => array('data'   => "ex3.n3",
                'query'  => "ex3",
                'result' => "ex3") ,

     3 => array('data'   => "ex11.2.3.1_0.n3",
                'query'  => "ex11.2.3.1_0",
                'result' => "ex11.2.3.1_0") ,

     4 => array('data'   => "ex11.2.3.2_0.n3",
                'query'  => "ex11.2.3.2_1",
                'result' => "ex11.2.3.2_1") ,

     5 => array('data'   => "ex11.2.3.3_0.n3",
                'query'  => "ex11.2.3.3_0",
                'result' => "ex11.2.3.3_0") ,

     6 => array('data'   => "ex11.2.3.4_0.n3",
                'query'  => "ex11.2.3.4_0",
                'result' => "ex11.2.3.4_0") ,

     7 => array('data'   => "ex11.2.3.5_0.n3",
                'query'  => "ex11.2.3.5_0",
                'result' => "ex11.2.3.5_0") ,

     8 => array('data'   => "ex11.2.3.7_0.n3",
                'query'  => "ex11.2.3.7_0",
                'result' => "ex11.2.3.7_0") ,

     9 => array('data'   => "data-f1.n3",
                'query'  => "expr-1",
                'result' => "expr-1") ,

    10 => array('data'   => "data-f1.n3",
                'query'  => "expr-2",
                'result' => "expr-2") ,

    11 => array('data'   => "data-f1.n3",
                'query'  => "expr-3",
                'result' => "expr-3") ,

    12 => array('data'   => "data-bool1.n3",
                'query'  => "query-bev-1",
                'result' => "query-bev-1") ,

    13 => array('data'   => "data-bool1.n3",
                'query'  => "query-bev-2",
                'result' => "query-bev-2") ,

    14 => array('data'   => "data-bool2.n3",
                'query'  => "query-bev-5",
                'result' => "query-bev-5") ,

    15 => array('data'   => "data-bool2.n3",
                'query'  => "query-bev-6",
                'result' => "query-bev-6") ,

    16 => array('data'   => "data-fil1.n3",
                'query'  => "q-str-1",
                'result' => "q-str-1") ,

    17 => array('data'   => "regex-data-01.n3",
                'query'  => "regex-query-001",
                'result' => "regex-query-001") ,


    18 => array('data'   => "regex-data-01.n3",
                'query'  => "regex-query-002",
                'result' => "regex-query-002") ,


    19 => array('data'   => "regex-data-01.n3",
                'query'  => "regex-query-003",
                'result' => "regex-query-003") ,

    20 => array('data'   => "ex11.2.3.6_0.n3",
                'query'  => "ex11.2.3.6_0",
                'result' => "ex11.2.3.6_0") ,

    21 => array('data'   => "data-bool1.n3",
                'query'  => "query-bev-3",
                'result' => "query-bev-3") ,

    22 => array('data'   => "data-bool1.n3",
                'query'  => "query-bev-4",
                'result' => "query-bev-4") ,

    23 => array('data'   => "data-fil1.n3",
                'query'  => "q-str-2",
                'result' => "q-str-2") ,

    24 => array('data'   => "data-fil1.n3",
                'query'  => "q-str-3",
                'result' => "q-str-3") ,

    25 => array('data'   => "data-fil1.n3",
                'query'  => "q-str-4",
                'result' => "q-str-4") ,

    26 => array('data'   => "data-fil1.n3",
                'query'  => "q-blank-1",
                'result' => "q-blank-1") ,

    // changed because of our N3 Parser
    27 => array('data'   => "data-fil1_changed.n3",
                'query'  => "q-datatype-1",
                'result' => "q-datatype-1") ,


    // FAILS: because no type error is thrown (compare xsd:date typed with
    // untyped Literal)
    29 => array('data'   => "ex11_0.n3",
                'query'  => "ex11_0",
                'result' => "ex11_0") ,

    30 => array('data'   => "ex11_0.n3",
                'query'  => "ex11_1",
                'result' => "ex11_1") ,

    31 => array(('data') => "ex11.2.3.1_1.n3",
                'query'  => "ex11.2.3.1_1",
                'result' => "ex11.2.3.1_1") ,
/**/
    32 => array(('data') => "ex11.2.3.2_0.n3",
                'query'  => "ex11.2.3.2_0",
                'result' => "ex11.2.3.2_0") ,

    33 => array('data'   => "data-fil1.n3",
                'query'  => "q-uri-1",
                'result' => "q-uri-1") ,

    34 => array('data'   => "data-langMatches.n3",
                'query'  => "q-langMatches-1",
                'result' => "q-langMatches-1") ,

    35 => array('data'   => "data-langMatches.n3",
                'query'  => "q-langMatches-2",
                'result' => "q-langMatches-2") ,

    36 => array('data'   => "data-langMatches.n3",
                'query'  => "q-langMatches-3",
                'result' => "q-langMatches-3") ,

    // FAILS: langMatches(String String) returns false if first arg is no Literal
//FIXME: cweiske thinks the test is broken or he does not understand it
//    37 => array('data'   => "data-langMatches.n3",
//                'query'  => "q-langMatches-4",
//                'result' => "q-langMatches-4") ,


    38 => array('data'   => "data-eq.n3",
                'query'  => "query-eq-1",
                'result' => "query-eq-1") ,

    39 => array('data'   => "data-eq.n3",
                'query'  => "query-eq-2",
                'result' => "query-eq-2") ,

    39 => array('data'   => "data-eq.n3",
                'query'  => "query-eq-2",
                'result' => "query-eq-2") ,

    40 => array('data'   => "data-eq.n3",
                'query'  => "query-eq-3",
                'result' => "query-eq-3") ,

    41 => array('data'   => "data-eq.n3",
                'query'  => "query-eq-4",
                'result' => "query-eq-4") ,

    42 => array('data'   => "data-eq.n3",
                'query'  => "query-eq-5",
                'result' => "query-eq-5") ,
//test is broken - query results and query return variables don't even match
//     43 => array('data'   => "data-eq.n3",
//                 'query'  => "query-eq2-1",
//                 'result' => "query-eq2-1") ,

/**/

    45 => array('data'   => "data-eq.n3",
                'query'  => "query-eq-graph-1",
                'result' => "query-eq-graph-1") ,

    46 => array('data'   => "data-eq.n3",
                'query'  => "query-eq-graph-2",
                'result' => "query-eq-graph-2") ,

    47 => array('data'   => "data-eq.n3",
                'query'  => "query-eq-graph-3",
                'result' => "query-eq-graph-3") ,

    48 => array('data'   => "data-eq.n3",
                'query'  => "query-eq-graph-4",
                'result' => "query-eq-graph-4") ,
/**/
);



$_SESSION['sparql_arq_tests'] = array(
/**/
    (1)  => array('data'   => "data-arq-basic.n3",
                'query'  => "q-base-prefix-1",
                'result' => "q-base-prefix-1") ,

    (2)  => array('data'   => "data-arq-basic.n3",
                'query'  => "q-base-prefix-2",
                'result' => "q-base-prefix-2") ,

    (3)  => array('data'   => "data-arq-basic.n3",
                'query'  => "q-base-prefix-3",
                'result' => "q-base-prefix-3") ,

    (4)  => array('data'   => "data-arq-basic.n3",
                'query'  => "q-base-prefix-4",
                'result' => "q-base-prefix-4") ,

    (5)  => array('data'   => "data-arq-basic.n3",
                'query'  => "q-base-prefix-5",
                'result' => "q-base-prefix-5") ,

    (6)  => array('data'   => "data-construct1.n3",
                'query'  => "q-construct-1",
                'result' => "q-construct-1") ,


    (8)  => array('data'   => "data-construct1.n3",
                'query'  => "q-construct-2",
                'result' => "q-construct-2") ,

    (9)  => array('data'   => "reif-data-1.n3",
                'query'  => "q-reif-1",
                'result' => "q-reif-1") ,

    (10) => array('data'   => "reif-data-2.n3",
                'query'  => "q-reif-2",
                'result' => "q-reif-2") ,

    (11) => array('data'   => "model0.nt",
                'query'  => "test-0-01",
                'result' => "test-0-01") ,

    (12) => array('data'   => "model0.nt",
                'query'  => "test-0-02",
                'result' => "test-0-02") ,

    (13) => array('data'   => "model0.nt",
                'query'  => "test-0-03",
                'result' => "test-0-03") ,

    (14) => array('data'   => "model0.nt",
                'query'  => "test-0-04",
                'result' => "test-0-04") ,

    (15) => array('data'   => "model1.nt",
                'query'  => "test-1-01",
                'result' => "test-1-01") ,

    (16) => array('data'   => "model1.nt",
                'query'  => "test-1-02",
                'result' => "test-1-02") ,

    (17) => array('data'   => "model1.nt",
                'query'  => "test-1-03",
                'result' => "test-1-03") ,

    (18) => array('data'   => "model1.nt",
                'query'  => "test-1-04",
                'result' => "test-1-04") ,

    (19) => array('data'   => "model1.nt",
                'query'  => "test-1-05",
                'result' => "test-1-05") ,


    (20) => array('data'   => "model1.nt",
                'query'  => "test-1-06",
                'result' => "test-1-06") ,

    (21) => array('data'   => "model1.nt",
                'query'  => "test-1-07",
                'result' => "test-1-07") ,

    (22) => array('data'   => "model1.nt",
                'query'  => "test-1-08",
                'result' => "test-1-08") ,

    (23) => array('data'   => "model1.nt",
                'query'  => "test-1-09",
                'result' => "test-1-09") ,

    (24) => array('data'   => "model1.nt",
                'query'  => "test-1-10",
                'result' => "test-1-10") ,

    (25) => array('data'   => "model1.nt",
                'query'  => "test-2-01",
                'result' => "test-2-01") ,

    (26) => array('data'   => "model1.nt",
                'query'  => "test-2-02",
                'result' => "test-2-02") ,

    (27) => array('data'   => "model1.nt",
                'query'  => "test-2-03",
                'result' => "test-2-03") ,

    (28) => array('data'   => "model1.nt",
                'query'  => "test-2-04",
                'result' => "test-2-04") ,

    (29) => array('data'   => "model1.nt",
                'query'  => "test-2-05",
                'result' => "test-2-05") ,

    (30) => array('data'   => "model1.nt",
                'query'  => "test-2-06",
                'result' => "test-2-06") ,

    (31) => array('data'   => "model1.nt",
                'query'  => "test-2-07",
                'result' => "test-2-07") ,

    (32) => array('data'   => "model1.nt",
                'query'  => "test-2-08",
                'result' => "test-2-08") ,

    (33) => array('data'   => "model1.nt",
                'query'  => "test-2-09",
                'result' => "test-2-09") ,

    (34) => array('data'   => "model1.nt",
                'query'  => "test-2-10",
                'result' => "test-2-10") ,
/**/
    (35) => array('data'   => "model1.nt",
                'query'  => "test-3-01",
                'result' => "test-3-01") ,

    (36) => array('data'   => "model1.nt",
                'query'  => "test-3-02",
                'result' => "test-3-02") ,

    (37) => array('data'   => "model1.nt",
                'query'  => "test-3-03",
                'result' => "test-3-03") ,

                // mehr als eine CONSTRAINT??
    (38) => array('data'   => "model1.nt",
                'query'  => "test-3-04",
                'result' => "test-3-04") ,

    (39) => array('data'   => "model1.nt",
                'query'  => "test-3-05",
                'result' => "test-3-05") ,

    (40) => array('data'   => "model1.nt",
                'query'  => "test-3-06",
                'result' => "test-3-06") ,

    (41) => array('data'   => "model5.nt",
                'query'  => "test-3-07",
                'result' => "test-3-07") ,

    (42) => array('data'   => "model2.nt",
                'query'  => "test-4-01",
                'result' => "test-4-01") ,

    (43) => array('data'   => "model2.nt",
                'query'  => "test-4-02",
                'result' => "test-4-02") ,

    (44) => array('data'   => "model2.nt",
                'query'  => "test-4-03",
                'result' => "test-4-03") ,

    (45) => array('data'   => "model2.nt",
                'query'  => "test-4-04",
                'result' => "test-4-04") ,

    (46) => array('data'   => "model3.nt",
                'query'  => "test-4-05",
                'result' => "test-4-05") ,

                //ARQ fails
    (47) => array('data'   => "model3.nt",
                'query'  => "test-4-06",
                'result' => "test-4-06") ,

    (48) => array('data'   => "model3.nt",
                'query'  => "test-4-07",
                'result' => "test-4-07") ,

    (49) => array('data'   => "model4.nt",
                'query'  => "test-5-01",
                'result' => "test-5-01") ,
/**/
    (50) => array('data'   => "model4.nt",
                'query'  => "test-5-02",
                'result' => "test-5-02") ,

    (51) => array('data'   => "model4.nt",
                'query'  => "test-5-03",
                'result' => "test-5-03") ,

    (52) => array('data'   => "model4.nt",
                'query'  => "test-5-04",
                'result' => "test-5-04") ,

    (53) => array('data'   => "model4.nt",
                'query'  => "test-6-01",
                'result' => "test-6-01") ,

    (54) => array('data'   => "model4.nt",
                'query'  => "test-6-02",
                'result' => "test-6-02") ,

    (55) => array('data'   => "model4.nt",
                'query'  => "test-6-03",
                'result' => "test-6-03") ,

    (56) => array('data'   => "model6.nt",
                'query'  => "test-7-01",
                'result' => "test-7-01") ,

    (57) => array('data'   => "model6.nt",
                'query'  => "test-7-02",
                'result' => "test-7-02") ,

    (58) => array('data'   => "model7.nt",
                'query'  => "test-7-03",
                'result' => "test-7-03") ,

    (59) => array('data'   => "model7.nt",
                'query'  => "test-7-04",
                'result' => "test-7-04") ,

    (60) => array('data'   => "model1.nt",
                'query'  => "test-9-01",
                'result' => "test-9-01") ,

    (60) => array('data'   => "model1.nt",
                'query'  => "test-9-02",
                'result' => "test-9-02") ,
    // FAILS: untyped Literal "value" == typed Literal "value"^^xsd:string ?
    (61) => array('data'   => "model8.n3",
                'query'  => "test-B-01",
                'result' => "test-B-01") ,

    (62) => array('data'   => "model8.n3",
                'query'  => "test-B-02",
                'result' => "test-B-02") ,

    (63) => array('data'   => "model8.n3",
                'query'  => "test-B-03",
                'result' => "test-B-03") ,

    (64) => array('data'   => "model8.n3",
                'query'  => "test-B-04",
                'result' => "test-B-04") ,

    (65) => array('data'   => "model8.n3",
                'query'  => "test-B-05",
                'result' => "test-B-05") ,
/**/
    ///////////// model9.n3 changed because of N3 Parser
    (67) => array('data'   => "model9.n3",
                'query'  => "test-B-08",
                'result' => "test-B-08") ,

    (68) => array('data'   => "model9.n3",
                'query'  => "test-B-09",
                'result' => "test-B-09") ,

    (69) => array('data'   => "model9.n3",
                'query'  => "test-B-10",
                'result' => "test-B-10") ,

    (70) => array('data'   => "model9.n3",
                'query'  => "test-B-11",
                'result' => "test-B-11") ,
    // FAILS because of type error
    (71) => array('data'   => "model9.n3",
                'query'  => "test-B-12",
                'result' => "test-B-12") ,

    (72) => array('data'   => "model9.n3",
                'query'  => "test-B-13",
                'result' => "test-B-13") ,

    // FAILS because 5==true
    (73) => array('data'   => "model9.n3",
                'query'  => "test-B-15",
                'result' => "test-B-15") ,

    // FAILS because of type error
    (74) => array('data'   => "model9.n3",
                'query'  => "test-B-17",
                'result' => "test-B-17") ,

    // FAILS because of type error
    (75) => array('data'   => "model9.n3",
                'query'  => "test-B-18",
                'result' => "test-B-18") ,


    (76) => array('data'   => "model9.n3",
                'query'  => "test-B-19",
                'result' => "test-B-19") ,
    // FAILS because of type error
    (77) => array('data'   => "model9.n3",
                'query'  => "test-B-20",
                'result' => "test-B-20") ,
);



$_SESSION['sparql_ask_tests'] = array(
     1 => array('data'   => 'dawg-query-001.n3',
                'query'  => "ask-01",
                'result' => "ask-01"),
     2 => array('data'   => 'dawg-query-001.n3',
                'query'  => "count-02",
                'result' => "count-02"),
);


require_once dirname(__FILE__) . '/cases_dawg2.php';
?>
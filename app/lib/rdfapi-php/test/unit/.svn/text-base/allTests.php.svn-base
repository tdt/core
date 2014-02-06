<?php

// ----------------------------------------------------------------------------------
// Script: allTest.php
// ----------------------------------------------------------------------------------

/**
 * Script for running the RAP unit tests.
 *
 * For running the tests you have to
 *
 * 1. Install the "Simple Test" testing framework
 *   into the document root of your web server.
 *   Simple test can be downloaded from:
 *   http://sourceforge.net/projects/simpletest/
 *
 * 2. Now copy the "unit" folder to /rdfapi/test/
 *
 * 3. Make sure that "simple Test" and RAP is included correctly in
 *   allTest.php and in
 *   showPasses.php
 *
 * 4. To run the tests execute allTest.php
 *
 * @version  $Id$
 * @author Tobias Gauß <tobias.gauss@web.de>
 *
 * @package unittests
 * @access	public
 */

//tests take some time
set_time_limit(10*60);

if (!@include_once(dirname(__FILE__) . '/../config.php')) {
    die('Make a copy of test/config.php.dist, change it and save it as test/config.php');
}

define('RDFS_INF_TESTFILES', RDFAPI_TEST_INCLUDE_DIR . 'test/unit/InfModel/');
if (!defined('SPARQL_TESTFILES')) {
    define('SPARQL_TESTFILES'  , RDFAPI_TEST_INCLUDE_DIR . 'test/unit/Sparql/');
}
define('MODEL_TESTFILES'   , RDFAPI_TEST_INCLUDE_DIR . 'test/unit/Model/');


require_once SIMPLETEST_INCLUDE_DIR . 'unit_tester.php';
require_once SIMPLETEST_INCLUDE_DIR . 'reporter.php';
require_once 'show_passes.php';
require_once RDFAPI_INCLUDE_DIR . "RdfAPI.php";

require_once RDFAPI_INCLUDE_DIR . PACKAGE_INFMODEL;
require_once RDFAPI_INCLUDE_DIR . PACKAGE_RESMODEL;
require_once RDFAPI_INCLUDE_DIR . PACKAGE_ONTMODEL;
require_once RDFAPI_INCLUDE_DIR . PACKAGE_SYNTAX_N3;
require_once RDFAPI_INCLUDE_DIR . PACKAGE_SYNTAX_RDF;
require_once RDFAPI_INCLUDE_DIR . PACKAGE_VOCABULARY;
require_once RDFAPI_INCLUDE_DIR . PACKAGE_DATASET;
require_once RDFAPI_INCLUDE_DIR . PACKAGE_SPARQL;
include(RDFAPI_INCLUDE_DIR.'vocabulary/ATOM_C.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/ATOM_RES.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/DC_C.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/DC_RES.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/FOAF_C.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/FOAF_RES.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/OWL_C.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/OWL_RES.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/RDF_C.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/RDF_RES.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/RDFS_C.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/RDFS_RES.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/RSS_C.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/RSS_RES.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/VCARD_C.php');
include(RDFAPI_INCLUDE_DIR.'vocabulary/VCARD_RES.php');


$_SESSION['passes']=0;
$_SESSION['fails']=0;
if (!defined('LOG')) {
    define('LOG', true);
}

if(LOG){
    $file = fopen ("testlog.log", "a");
    $time= strftime('%d.%m.%y %H:%M:%S' );
    fputs($file,"\r\n".'-----'.$time.'-----'."\r\n");
}

if (TextReporter::inCli()) {
    $runnerClass = 'TextReporter';
} else {
    $runnerClass = 'ShowPasses';
}


// =============================================================================
// *************************** package Model ***********************************
// =============================================================================
$test_model = &new GroupTest('Model tests');
$test_model->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/Model/Model_tests.php');
$test_model->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/Model/mm_SetOperations_tests.php');
$test_model->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Model/mm_BasicOperations_tests.php');
$test_model->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Model/mm_index_tests.php');
$test_model->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Model/literals_tests.php');
$test_model->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Model/blanknode_test.php');
$test_model->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Model/dBModel_test.php');
$test_model->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/Model/mm_search_tests.php');
$test_model->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/Model/getModelByRDQL_tests.php');
$test_model->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/Model/modelEquals_test.php');

$test_model->run(new $runnerClass());

// =============================================================================
// *************************** package Syntax **********************************
// =============================================================================

$test_syntax = &new GroupTest('Syntax tests');
$test_syntax->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Syntax/n3Parser_test.php');
$test_syntax->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Syntax/n3Serializer_test.php');
$test_syntax->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Syntax/rdf_Parser_tests.php');
$test_syntax->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Syntax/rdf_Serializer_tests.php');
$test_syntax->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Syntax/jsonParser_test.php');
$test_syntax->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Syntax/jsonSerializer_test.php');
//$test_syntax->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/rdf/rdf_test_cases.php');
$test_syntax->run(new $runnerClass());

// =============================================================================
// *************************** package Utility *********************************
// =============================================================================

$test_util = &new GroupTest('Utility tests');
$test_util->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/utility/ut_FindIterator_tests.php');
$test_util->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/utility/ut_it_tests.php');
$test_util->run(new $runnerClass());
 // =============================================================================
// *************************** package InfModel ********************************
// =============================================================================

$test_inf= &new GroupTest('Model InfModel tests');
$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelF_SetOperations_tests.php');
$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelB_SetOperations_tests.php');
$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/InfModel/InfModelF_BasicOperations_tests.php');
$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/InfModel/InfModelB_BasicOperations_tests.php');
$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/InfModel/InfModelF_index_tests.php');
$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelF_search_tests.php');
$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelB_search_tests.php');
$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelF_jena_rdfs_test.php');
$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelB_jena_rdfs_test.php');
$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelF_entailment_test.php');
//$test_inf->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelB_entailment_test.php');
$test_inf->run(new $runnerClass());

// =============================================================================
// *************************** package ResModel ********************************
// =============================================================================
$test_res= &new GroupTest('ResModel basic operations tests');
$test_res->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/ResModel/ResModel_BasicOperations_tests.php');
$test_res->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/ResModel/ResModel_Property_tests.php');
$test_res->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/ResModel/ResModel_search_tests.php');
$test_res->run(new $runnerClass());

// =============================================================================
// *************************** package OntModel ********************************
// =============================================================================
$test_ont= &new GroupTest('OntModel basic operations tests');
$test_ont->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/OntModel/OntModel_BasicOperations_tests.php');
$test_ont->run(new $runnerClass());


// =============================================================================
// *************************** package vocabulary ******************************
// =============================================================================

$test_voc= &new GroupTest('Vocabulary tests');
//$test_voc->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Vocabulary/voc_tests.php');
$test_voc->run(new $runnerClass());

// =============================================================================
// *************************** namespace handling ******************************
// =============================================================================


$test_nms= &new GroupTest('Namespace tests');
$test_nms->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Namespaces/Namespace_handling_tests.php');
$test_nms->run(new $runnerClass());


// =============================================================================
// ************************** named graphs api tests****************************
// =============================================================================

$test_ng= &new GroupTest('Named Graphs api tests');
$test_ng->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Namedgraphs/Namedgraphs_tests.php');
$test_ng->run(new $runnerClass());

// =============================================================================
// ******************************* sparql tests ********************************
// =============================================================================

$test_sparql= &new GroupTest('Sparql Testcases');
$test_sparql->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Sparql/ResultParserTests_test.php');
$test_sparql->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Sparql/SparqlParserTests_test.php');
$test_sparql->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Sparql/SparqlTests_test.php');
$test_sparql->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Sparql/SparqlDbTests_test.php');
$test_sparql->run(new $runnerClass());




if (LOG) {
    $file = fopen ("testlog.log", "a");
    $time = strftime('%d.%m.%y %H:%M:%S' );
    fputs($file,
        "\r\n"
        . ' Passes: ' . $_SESSION['passes']
        . ' Fails: ' . $_SESSION['fails']
        . "\r\n"
    );
    fclose($file);
}
?>
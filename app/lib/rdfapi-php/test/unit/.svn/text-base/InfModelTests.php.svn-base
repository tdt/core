<?php

// ----------------------------------------------------------------------------------
// Script: allTest.php
// ----------------------------------------------------------------------------------

/**
 * Script for running the tests on the InfModelF & InfModelB.
 *
 * <BR><BR>History:<UL>
 * <LI>09-15-2004				 : Initial version of this class.
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
 * 4. To run the tests execute InfTests.php
 *
 * @version  V0.9.1
 * @author Daniel Westphal	<mail at d-westphal dot de>
 *
 * @package unittests
 * @access	public
 */

	
	define("SIMPLETEST_INCLUDE_DIR", "C:/!htdocs/simpletest/");
	define("RDFAPI_INCLUDE_DIR", "C:/!htdocs/rdfapi-php/api/");
	define("RDFAPI_TEST_INCLUDE_DIR", "C:/!htdocs/rdfapi-php/");
	include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
	require_once( SIMPLETEST_INCLUDE_DIR . 'unit_tester.php');
	require_once( SIMPLETEST_INCLUDE_DIR . 'reporter.php');
	include_once( RDFAPI_INCLUDE_DIR . PACKAGE_INFMODEL);
	include_once( RDFAPI_INCLUDE_DIR . PACKAGE_SYNTAX_N3);
	require_once(RDFAPI_TEST_INCLUDE_DIR.'test/unit/show_passes.php');
  	define('RDFS_INF_TESTFILES',RDFAPI_TEST_INCLUDE_DIR.'test/unit/Infmodel/');
	$_SESSION['passes']=0;
	$_SESSION['fails']=0;
	define('LOG',FALSE);
	
	if(LOG){
		$file = fopen ("testlog.log", "a");
		$time= strftime('%d.%m.%y %H:%M:%S' );
		fputs($file,"\r\n".'-----'.$time.'-----'."\r\n");
	}
	
// =============================================================================
// *************************** package InfModel ***********************************
// =============================================================================

    $test1a= &new GroupTest('Model InfModelF Set Operations Test');
    $test1a->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelF_SetOperations_tests.php');
    $test1a->run(new ShowPasses());
   
    $test1b= &new GroupTest('Model InfModelB Set Operations Test');
    $test1b->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelB_SetOperations_tests.php');
    $test1b->run(new ShowPasses());
   
    $test2a= &new GroupTest('Model InfModelF Basic Operations Test');
    $test2a->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/InfModel/InfModelF_BasicOperations_tests.php');
    $test2a->run(new ShowPasses());
	
    $test2b= &new GroupTest('Model InfModelB Basic Operations Test');
    $test2b->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/InfModel/InfModelB_BasicOperations_tests.php');
    $test2b->run(new ShowPasses());
    
    $test3a= &new GroupTest('Model InfModelF Indextest');
    $test3a->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/InfModel/InfModelF_index_tests.php');
    $test3a->run(new ShowPasses());

    $test3b= &new GroupTest('Model InfModelB Indextest');
    $test3b->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/InfModel/InfModelB_index_tests.php');
    $test3b->run(new ShowPasses());
    
    $test4a= &new GroupTest('Model InfModelF Search Test');
    $test4a->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelF_search_tests.php');
    $test4a->run(new ShowPasses()); 
  
    $test4b= &new GroupTest('Model InfModelB Search Test');
    $test4b->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelB_search_tests.php');
    $test4b->run(new ShowPasses()); 

    $test5a= &new GroupTest('Model InfModelF : jena RDFS-tests ');
   	$test5a->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelF_jena_rdfs_test.php');
    $test5a->run(new ShowPasses());
    
    $test5b= &new GroupTest('Model InfModelB : jena RDFS-tests ');
    $test5b->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelB_jena_rdfs_test.php');
    $test5b->run(new ShowPasses());
   
    $test6a= &new GroupTest('InfModelF entailment tests');
    $test6a->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelF_entailment_test.php');
    $test6a->run(new ShowPasses());
 
    $test6b= &new GroupTest('InfModelB entailment tests');
    $test6b->addTestFile(RDFAPI_TEST_INCLUDE_DIR. '/test/unit/InfModel/InfModelB_entailment_test.php');
    $test6b->run(new ShowPasses()); 

    if(LOG){
   		 $file = fopen ("testlog.log", "a");
   		 $time= strftime('%d.%m.%y %H:%M:%S' );
	     fputs($file,"\r\n".' Passes: '.$_SESSION['passes'].' Fails: '.$_SESSION['fails']."\r\n");
 	     fclose($file);
    }
?>

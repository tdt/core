<?php

if (!@include_once(dirname(__FILE__) . '/../config.php')) {
    die('Make a copy of test/config.php.dist, change it and save it as test/config.php');
}

require_once SIMPLETEST_INCLUDE_DIR . 'unit_tester.php';
require_once SIMPLETEST_INCLUDE_DIR . 'reporter.php';
require_once 'show_passes.php';
require(RDFAPI_INCLUDE_DIR . 'RdfAPI.php');

$_SESSION['passes'] = 0;
$_SESSION['fails']  = 0;

if(LOG){
    $file = fopen ("testlog.log", "a");
    $time= strftime('%d.%m.%y %H:%M:%S' );
    fputs($file,"\r\n".'-----'.$time.'-----'."\r\n");
}

$test_sparql= &new GroupTest('Sparql Parser Testcases');
$test_sparql->addTestFile(RDFAPI_TEST_INCLUDE_DIR. 'test/unit/Sparql/SparqlParserTests_test.php');
//$test_sparql->run(new ShowPasses());
$test_sparql->run(new TextReporter());
//$test_sparql->run(new EarlReporter());


if(LOG){
    $file = fopen ("testlog.log", "a");
    $time= strftime('%d.%m.%y %H:%M:%S' );
    fputs($file,"\r\n".' Passes: '.$_SESSION['passes'].' Fails: '.$_SESSION['fails']."\r\n");
    fclose($file);
}

?>
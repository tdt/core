<?php
/**
* Executes any given simpletest case.
* Simply pass the filename
*
* Example usage:
* php runAnyTest.php --earl \
*  Sparql/SparqlDbTests_test.php \
*  Sparql/SparqlParserTests_test.php \
*  > earl-results-rap-2007-10-09.n3
*
*/
if (!@include_once(dirname(__FILE__) . '/../config.php')) {
    die('Make a copy of test/config.php.dist, change it and save it as test/config.php');
}

if ($argc <= 1 || $argv[1] == '--help') {
    echo <<<EOT
Run any simpletest files.
 Usage: php runAnyTest.php [--earl] file(s)

 --earl     Generate report in EARL format
 --help     Show this help screen


EOT;
    exit(1);
}

$files       = array();
$reportClass = 'TextReporter';
array_shift($argv);

foreach ($argv as $option) {
    if ($option == '--earl') {
        require_once dirname(__FILE__) . '/EarlReporter.php';
        $reportClass = 'EarlReporter';
    } else {
        //file?
        if (!file_exists($option)) {
            echo "File $option does not exist\n";
            exit(2);
        }
        $files[] = $option;
    }
}

require_once SIMPLETEST_INCLUDE_DIR . 'unit_tester.php';
require_once SIMPLETEST_INCLUDE_DIR . 'reporter.php';
require_once 'show_passes.php';
require_once RDFAPI_INCLUDE_DIR . 'RdfAPI.php';

$_SESSION['passes'] = 0;
$_SESSION['fails']  = 0;

$test_sparql = new GroupTest('some RDF API for PHP tests');
foreach ($files as $file) {
    $test_sparql->addTestFile($file);
}

//$test_sparql->run(new ShowPasses());
$test_sparql->run(new $reportClass());


?>
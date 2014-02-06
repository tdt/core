<?php
/**
* Utility script to generate an array of dawg2 tests similar to
* cases.php from the n3 tests notation.
* We could read the n3 test definition files everytime we run the tests,
* but this takes too long - so we cache it.
*
* Usage:
*  php create-dawg2.php > cases_dawg2.php
*/
require_once dirname(__FILE__) . '/../../config.php';
require_once 'Dawg2Helper.php';

Dawg2Helper::loadDawg2Tests();

echo "<?php
/**
* automatically created by create-dawg2.php on " . date('Y-m-d H:i') . "
*/
\$_SESSION['sparql_dawg2_tests'] = ";
var_export($_SESSION['sparql_dawg2_tests']);
echo ";
?>";
?>
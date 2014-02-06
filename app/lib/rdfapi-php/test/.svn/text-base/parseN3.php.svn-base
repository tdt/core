<?php
/**
*   Small script to try out our N3 parser.
*   Simply call it with the file to parse as parameter.
*
*   @author Christian Weiske <cweiske@cweiske.de>
*/
if ($argc <= 1) {
    echo <<<EOT
Parses an N3 ("turtle") file
 Usage: php parseN3.php [--debug] /path/to/file.n3

EOT;
    exit(1);
}

if ($argc == 3 && $argv[1] == '--debug') {
    $bDebug = true;
    array_shift($argv);
} else {
    $bDebug = false;
}

$file = $argv[1];
if (!file_exists($file)) {
    echo <<<EOT
File does not exist.
 Usage: php parseN3.php [--debug] /path/to/file.n3

EOT;
    exit(2);
}


require_once 'config.php';
require_once RDFAPI_INCLUDE_DIR . 'syntax/N3Parser.php';

$parser = new N3Parser();
if ($bDebug) {
    $parser->debug = true;
}
$parser->parse(
    file_get_contents($file)
);

?>
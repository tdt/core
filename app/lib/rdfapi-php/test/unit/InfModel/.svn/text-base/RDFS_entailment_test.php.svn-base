<?php 

//change the RDFAPI_INCLUDE_DIR to your local settings
define("RDFAPI_INCLUDE_DIR", "C:/!htdocs/rdfapi-php/api/"); 
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include_once( RDFAPI_INCLUDE_DIR . PACKAGE_INFMODEL);
include_once( RDFAPI_INCLUDE_DIR . PACKAGE_SYNTAX_N3);

//uncomment the model you'd like to use
$inf2= new RDFSBModel('http://mytest.com');
$inf= new RDFSFModel('http://mytest.com');

$parser= new N3Parser();

$inf->addModel($parser->parse2model(
'
<http://example.org/baz1> <http://example.org/bat> <http://example.org/baz2> .
<http://example.org/bat> <http://www.w3.org/2000/01/rdf-schema#subPropertyOf> <http://example.org/bas> .
'
));
$inf2->addModel($parser->parse2model(
'
<http://example.org/baz1> <http://example.org/bat> <http://example.org/baz2> .
<http://example.org/bat> <http://www.w3.org/2000/01/rdf-schema#subPropertyOf> <http://example.org/bas> .
'
));

echo '<B>Added the following Triples:</B><BR>'.htmlentities('
<http://example.org/baz1> <http://example.org/bat> <http://example.org/baz2> .').'<BR>'.htmlentities('
<http://example.org/bat> <http://www.w3.org/2000/01/rdf-schema#subPropertyOf> <http://example.org/bas> .
').'<BR>';
$inf->writeAsHtmlTable();
$inf2->writeAsHtmlTable();
#$sub=$inf2->subtract($inf);
#$sub->writeAsHtmlTable();
#echo '<BR><hr><BR>';
echo '<BR><hr><BR>';


$inf->add(new Statement(new Resource('http://example.org/bat'),new Resource('http://www.w3.org/2000/01/rdf-schema#domain'),new Resource('http://example.org/Domain1')));
$inf->add(new Statement(new Resource('http://example.org/bat'),new Resource('http://www.w3.org/2000/01/rdf-schema#range'),new Resource('http://example.org/Range1')));
$inf2->add(new Statement(new Resource('http://example.org/bat'),new Resource('http://www.w3.org/2000/01/rdf-schema#domain'),new Resource('http://example.org/Domain1')));
$inf2->add(new Statement(new Resource('http://example.org/bat'),new Resource('http://www.w3.org/2000/01/rdf-schema#range'),new Resource('http://example.org/Range1')));

echo '<B>Added the following Triples:</B><BR>'.htmlentities('
<http://example.org/bat> <http://www.w3.org/2000/01/rdf-schema#domain> <http://example.org/Domain1> .').'<BR>'.htmlentities('
<http://example.org/bat> <http://www.w3.org/2000/01/rdf-schema#range> <http://example.org/Range1> .
').'<BR>';
$inf->writeAsHtmlTable();
$inf2->writeAsHtmlTable();

echo '<BR><hr><BR>';


$inf->add(new Statement(new Resource('http://example.org/bas'),new Resource('http://www.w3.org/2000/01/rdf-schema#domain'),new Resource('http://example.org/Domain2')));
$inf->add(new Statement(new Resource('http://example.org/bas'),new Resource('http://www.w3.org/2000/01/rdf-schema#range'),new Resource('http://example.org/Range2')));
$inf2->add(new Statement(new Resource('http://example.org/bas'),new Resource('http://www.w3.org/2000/01/rdf-schema#domain'),new Resource('http://example.org/Domain2')));
$inf2->add(new Statement(new Resource('http://example.org/bas'),new Resource('http://www.w3.org/2000/01/rdf-schema#range'),new Resource('http://example.org/Range2')));

echo '<B>Added the following Triples :</B><BR>'.htmlentities('
<http://example.org/bas> <http://www.w3.org/2000/01/rdf-schema#domain> <http://example.org/Domain2> .').'<BR>'.htmlentities('
<http://example.org/bas> <http://www.w3.org/2000/01/rdf-schema#range> <http://example.org/Range2> .
').'<BR>';
$inf->writeAsHtmlTable();
$inf2->writeAsHtmlTable();
$ser= new N3Serializer();
echo htmlentities($ser->serialize($inf));
echo '<BR><hr><BR>';

$inf->add(new Statement(new Resource('http://example.org/Domain2'),new Resource('http://www.w3.org/2000/01/rdf-schema#subClassOf'),new Resource('http://example.org/Domain3')));
$inf->add(new Statement(new Resource('http://example.org/Domain3'),new Resource('http://www.w3.org/2000/01/rdf-schema#subClassOf'),new Resource('http://example.org/Domain2')));

$inf2->add(new Statement(new Resource('http://example.org/Domain2'),new Resource('http://www.w3.org/2000/01/rdf-schema#subClassOf'),new Resource('http://example.org/Domain3')));
$inf2->add(new Statement(new Resource('http://example.org/Domain3'),new Resource('http://www.w3.org/2000/01/rdf-schema#subClassOf'),new Resource('http://example.org/Domain2')));

echo '<B>Added the following Triples (loop in the ontology) :</B><BR>'.htmlentities('
<http://example.org/Domain2> <http://www.w3.org/2000/01/rdf-schema#subClassOf> <http://example.org/Domain3> .').'<BR>'.htmlentities('
<http://example.org/Domain3> <http://www.w3.org/2000/01/rdf-schema#subClassOf> <http://example.org/Domain2> .');
$inf->writeAsHtmlTable();
$inf2->writeAsHtmlTable();
echo '<BR><hr><BR>';



$inf->add(new Statement(new Resource('http://example.org/Range3'),new Resource('http://www.w3.org/2002/07/owl#sameAs'),new Resource('http://example.org/Range2')));
$inf2->add(new Statement(new Resource('http://example.org/Range3'),new Resource('http://www.w3.org/2002/07/owl#sameAs'),new Resource('http://example.org/Range2')));

echo '<B>Added the following Triple :</B><BR>'.htmlentities('
<http://example.org/Range3> <http://www.w3.org/2002/07/owl#sameAs> <http://example.org/Range2> .').'<BR>';
$inf->writeAsHtmlTable();
$inf2->writeAsHtmlTable();
echo '<BR><hr><BR>';


$findResult=$inf->find(new Resource('http://example.org/baz2'),null,null);
echo '<B>Perform a find(http://example.org/baz2,null,null) :</B><BR>';
$findResult->writeAsHtmlTable();
$findResult2=$inf2->find(new Resource('http://example.org/baz2'),null,null);
$findResult2->writeAsHtmlTable();
echo '<BR><hr><BR>';


$inf->remove(new Statement(new Resource('http://example.org/bat'),new Resource('http://www.w3.org/2000/01/rdf-schema#subPropertyOf'),new Resource('http://example.org/bas')));
$inf2->remove(new Statement(new Resource('http://example.org/bat'),new Resource('http://www.w3.org/2000/01/rdf-schema#subPropertyOf'),new Resource('http://example.org/bas')));

echo '<B>Removed the following Triple :</B><BR>'.htmlentities('
<http://example.org/bat> <http://www.w3.org/2000/01/rdf-schema#subPropertyOf> <http://example.org/bas> .').'<BR>';
$inf->writeAsHtmlTable();
$inf2->writeAsHtmlTable();


?>
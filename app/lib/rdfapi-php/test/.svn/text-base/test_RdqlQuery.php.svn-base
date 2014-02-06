<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Test Traverse MemModel</title>
</head>
<body>

<?php
define("RDFAPI_INCLUDE_DIR", "./../api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");

// Filename of an RDF document
$base="employees.rdf";

// Create a new MemModel
$model = new MemModel();

// Load and parse document
$model->load($base);

// Show input model
//$model->writeAsHtmlTable();


echo "<p>";


echo "<BR>show the age of all employees ,using the RdqlResultIterator<BR>"; 
$rdql_query = '
SELECT ?givenName, ?age
WHERE (?x, v:age, ?age),
      (?x, vcard:N, ?blank),
      (?blank, vcard:Given, ?givenName)
USING vcard FOR <http://www.w3.org/2001/vcard-rdf/3.0#>
      v FOR <http://sampleVocabulary.org/1.3/People#>';

//query model, get RDQLResultIterator
$rdqlIter = $model->rdqlQueryasIterator($rdql_query);

//get different Result labels as array
$result_labels=$rdqlIter->getResultLabels();

echo "<BR><b>Result Label Names:</b> ";
for ($i=0; $i <count($result_labels); $i++) echo  $result_labels[$i]." ";
echo "<BR><B>Number of results:</B> ".$rdqlIter->countResults(); 
echo "<BR><B>Result objects, serialized to string:</B><BR>"; 
//serialize result to string
while ($rdqlIter->hasNext()) {
	$curren_result=$rdqlIter->next();
	echo "<BR>";
	for ($j=0; $j <count($result_labels); $j++) echo  $curren_result[$result_labels[$j]]->toString()."<BR> ";
};



echo "</p>";

// show emails of all employees
$rdql_query = '
SELECT ?givenName, ?familyName, ?email
WHERE  (?person vcard:N ?blank1)
       (?blank1 vcard:Given ?givenName)
       (?blank1 vcard:Family ?familyName)
       (?person vcard:EMAIL ?blank2)
       (?blank2 rdf:value ?email)
AND ?person ~~ "/example.com\/EMPLOYEES\//i"
USING vcard FOR <http://www.w3.org/2001/vcard-rdf/3.0#>
      v FOR <http://sampleVocabulary.org/1.3/People#>';

$res = $model->rdqlQuery($rdql_query);

RdqlEngine::writeQueryResultAsHtmlTable($res);




echo "<br>show all employees over 30 <BR>";

// show all employees over 30
$rdql_query = '
SELECT ?employee, ?age
WHERE  (?x, vcard:FN, ?employee), (?x, <v:age>, ?age)
AND ?age > 30
USING vcard FOR <http://www.w3.org/2001/vcard-rdf/3.0#>
      v FOR <http://sampleVocabulary.org/1.3/People#>';


$res = $model->rdqlQuery($rdql_query);

RdqlEngine::writeQueryResultAsHtmlTable($res);



echo "<p>";


// find out whose number is it: +1 111 2212 431
$rdql_query = '
SELECT ?x, ?type
WHERE  (?x, vcard:TEL, ?tel),
       (?tel, rdf:value, ?telNumber)
       (?tel, rdf:type, ?type)
AND ?telNumber eq "+1 111 2212 431"
USING vcard FOR <http://www.w3.org/2001/vcard-rdf/3.0#>
      v FOR <http://sampleVocabulary.org/1.3/People#>';
$res = $model->rdqlQuery($rdql_query);
RdqlEngine::writeQueryResultAsHtmlTable($res);


echo "</p>";

// show office telephone numbers of all employees
$rdql_query = '
SELECT ?givenName, ?familyName, ?telNumber
WHERE  (?person vcard:N ?blank1)
       (?blank1 vcard:Given ?givenName)
       (?blank1 vcard:Family ?familyName)
       (?person vcard:TEL ?blank2)
       (?blank2 rdf:value ?telNumber)
       (?blank2 rdf:type ?type)
AND ?person ~~ "/example.com\/EMPLOYEES\//i" && ?type eq vcard:work
USING vcard FOR <http://www.w3.org/2001/vcard-rdf/3.0#>
      v FOR <http://sampleVocabulary.org/1.3/People#>';
$res = $model->rdqlQuery($rdql_query);
RdqlEngine::writeQueryResultAsHtmlTable($res);


?>



</body>
</html>

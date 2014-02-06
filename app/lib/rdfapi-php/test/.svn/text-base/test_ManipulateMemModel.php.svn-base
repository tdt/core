<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Test Manipulate and Search RDF Model</title>
</head>
<body>

<?php
// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");

// Filename of an RDF document
$base="example1.rdf";

// Create a new MemModel
$model = new MemModel();

// Load and parse document
$model->load($base);

// Output model as HTML table
$model->writeAsHtmlTable();
echo "<P>";

// Ceate new statements and add them to the model
$statement1 = new Statement(new Resource("http://www.w3.org/Home/Lassila"),
				  		    new Resource("http://description.org/schema/Description"),
					  		new Literal("Lassila's personal Homepage", "en"));
					  
$statement2 = new Statement(new Resource("http://www.w3.org/Home/Lassila"),
					  		new Resource("http://description.org/schema/Description"),
					  		new Literal("Lassilas persönliche Homepage ", "de"));

$model->add($statement1);
$model->add($statement2);

$model->writeAsHtmlTable();
echo "<P>";


// Search model 1
$homepage = new Resource("http://www.w3.org/Home/Lassila");
$res = $model->find($homepage, NULL, NULL);

$res->writeAsHtmlTable();
echo "<P>";

// Search model 2
$description = new Resource("http://description.org/schema/Description");
$statement = $model->findFirstMatchingStatement($homepage, $description, NULL);

// Check if something was found and output result
if ($statement) {
   echo $statement->toString();
} else {
	echo "Sorry, I didn't find anything.";
}
echo "<P>";

// Search model 3
$res3 = $model->findVocabulary("http://example.org/stuff/1.0/");
$res3->writeAsHtmlTable();
echo "<P>";
			  
// Write model as RDF
$model->writeAsHtml();

// Save model to file
$model->saveAs("Output.rdf");


?>
</body>
</html>

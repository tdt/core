<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Test Serializer Options</title>
</head>
<body>

<?php
define("RDFAPI_INCLUDE_DIR", "./../api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");

// Filename of an RDf document
$base="example1.rdf";

// Create a new MemModel
$model = new MemModel();

// Load and parse document
$model->load($base);

// Output model as HTML table
//$model->writeAsHtmlTable();
echo "<P>";

			  
// Create Serializer and serialize model to RDF with default configuration
$ser = new RDFSerializer();
$rdf =& $ser->serialize($model);
echo "<p><textarea cols='110' rows='20'>" . $rdf . "</textarea>";

// Serialize model to RDF using attributes
$ser->configUseAttributes(TRUE);
$rdf =& $ser->serialize($model);
echo "<p><textarea cols='110' rows='20'>" . $rdf . "</textarea>";
$ser->configUseAttributes(FALSE);
 
// Serialize model to RDF using entities
$ser->configUseEntities(TRUE);
$rdf =& $ser->serialize($model);
echo "<p><textarea cols='110' rows='30'>" . $rdf . "</textarea>";
$ser->configUseEntities(FALSE);

?>

</body>
</html>

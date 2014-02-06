<?php
/**
*   Nested queries parser test
*   While the result format might seem a bit strange, it helps
*   us to debug the results easily and spot what doesn't work at which place.
*
*   @author Christian Weiske <cweiske@cweiske.de>
*/
$GLOBALS['testSparqlParserTestsNested'] = array(
    array(<<<EOT
SELECT ?name ?mbox
WHERE
{
  ?a ?b ?c
  {
    {
      ?g ?h ?i .
      {
        {?person <some://typ/e> 'asd'}
        UNION
        {?person3 <some://typ/es2> 'three'}
      }
    }
    UNION
    {?person2 <some://typ/es> '2'}
  }
  ?d ?e ?f
}
EOT
        , <<<EOT
GP #0
  ?a, ?b, ?c
  ?d, ?e, ?f
  ?person2, Resource("some://typ/es"), Literal("2")
GP #2 unionWith(0)
  ?a, ?b, ?c
  ?d, ?e, ?f
  ?g, ?h, ?i
  ?person, Resource("some://typ/e"), Literal("asd")
GP #6 unionWith(0)
  ?a, ?b, ?c
  ?d, ?e, ?f
  ?g, ?h, ?i
  ?person3, Resource("some://typ/es2"), Literal("three")

EOT
    ),

    /////next\\\\\
    array( <<<EOT
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/1999/02/22-rdf-syntax-nss#>
SELECT DISTINCT ?Res ?Attr ?Val
WHERE
{
  {
    ?Res rdf:type ?_v1 .
    {
      ?Res foaf:name ?_v2 FILTER regex(?_v2,"^Alexander Loeser$", "i")
    }
    UNION
    {
      ?Res rdfs:label ?_v3 FILTER regex(?_v3,"^Alexander Loeser$", "i")
    }
  }
  ?Res ?Attr ?Val
}
ORDER BY ?Res
EOT
        , <<<EOT
GP #0 filter(1)
  ?Res, ?Attr, ?Val
  ?Res, Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#type"), ?_v1
  ?Res, Resource("http://www.w3.org/1999/02/22-rdf-syntax-nss#label"), ?_v3
GP #1 unionWith(0) filter(1)
  ?Res, ?Attr, ?Val
  ?Res, Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#type"), ?_v1
  ?Res, Resource("http://xmlns.com/foaf/0.1/name"), ?_v2

EOT
    ),

    /////next\\\\\
    array( <<<EOT
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>

SELECT ?name ?mbox
WHERE
  {
    { ?person rdf:type foaf:Person } .
    OPTIONAL { ?person foaf:name  ?name } .
    OPTIONAL {?person foaf:mbox  ?mbox} .
  }
  UNION
  {
    { ?person2 rdf:kind foaf:Person } .
    OPTIONAL {?person2 <asdf> ?mbox. ?person2 rdf:laughs ?atyou } .
  }
EOT
        , <<<EOT
GP #1
  ?person, Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#type"), Resource("http://xmlns.com/foaf/0.1/Person")
GP #2 optionalTo(1)
  ?person, Resource("http://xmlns.com/foaf/0.1/name"), ?name
GP #3 optionalTo(1)
  ?person, Resource("http://xmlns.com/foaf/0.1/mbox"), ?mbox

EOT
    ),

    /////next\\\\\
    array( <<<EOT
PREFIX foaf:       <http://xmlns.com/foaf/0.1/>
SELECT DISTINCT ?value
WHERE {
      {?s ?p ?value . FILTER (isIRI(?value)). OPTIONAL { ?s ?p ?value} }
UNION {?s ?value ?o . FILTER (isIRI(?value)) }
UNION {?value ?p ?o . FILTER (isIRI(?value)) }
      }
EOT
        , <<<EOT
GP #1 filter(1)
  ?s, ?p, ?value
GP #2 optionalTo(1)
  ?s, ?p, ?value
GP #3 unionWith(1) filter(1)
  ?s, ?value, ?o
GP #4 unionWith(3) filter(1)
  ?value, ?p, ?o

EOT
    ),

/*

    /////next\\\\\
    array( <<<EOT
EOT
        , <<<EOT
EOT
    ),
*/
);
?>
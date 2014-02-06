<?php
/**
* Filter parser tests
*/
$GLOBALS['testSparqlParserTestsFilter'] = array(
    array(
<<<EOT
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT ?s, ?o
{ ?s foaf:name ?o . FILTER (?s = ?o)}
EOT
,
        array(
            'type'      => 'equation',
            'operator'  => '=',
            'operand1'  => array('type' => 'value', 'value' => '?s', 'quoted' => false),
            'operand2'  => array('type' => 'value', 'value' => '?o', 'quoted' => false)
        )
    ),

    array(
<<<EOT
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
SELECT ?s, ?o
{ ?s foaf:name ?o . FILTER (! bound(?s))}
EOT
,
        array(
            'negated'   => true,
            'type'      => 'function',
            'name'      => 'bound',
            'parameter' => array(
                array('type' => 'value', 'value' => '?s', 'quoted' => false)
            ),
        )
    ),

    array(
<<<EOT
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
SELECT ?s, ?o
{ ?s foaf:name ?o . FILTER(?date < xsd:dateTime("2005-01-01T00:00:00Z"))}
EOT
,
        array(
            'type'      => 'equation',
            'operator'  => '<',
            'operand1'  => array('type' => 'value', 'value' => '?date', 'quoted' => false),
            'operand2'  => array(
                'type'      => 'function',
                'name'      => 'xsd:dateTime',
                'parameter' => array(
                    array(
                        'type'      => 'value',
                        'value'     => '2005-01-01T00:00:00Z',
                        'quoted'    => true
                    )
                )
            )
        )
    ),

    array(
<<<EOT
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
SELECT ?mbox1
{ ?name1 foaf:mbox ?mbox1 . FILTER ( (?mbox1 = ?mbox2) && (?name1 != ?name2) )}
EOT
,
        array(
            'type'      => 'equation',
            'operator'  => '&&',
            'operand1'  => array(
                'type'      => 'equation',
                'operator'  => '=',
                'operand1'  => array('type' => 'value', 'value' => '?mbox1', 'quoted' => false),
                'operand2'  => array('type' => 'value', 'value' => '?mbox2', 'quoted' => false)
            ),
            'operand2'  => array(
                'type'      => 'equation',
                'operator'  => '!=',
                'operand1'  => array('type' => 'value', 'value' => '?name1', 'quoted' => false),
                'operand2'  => array('type' => 'value', 'value' => '?name2', 'quoted' => false)
            )
        )
    ),

    array(
<<<EOT
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
SELECT ?mbox1
{ ?name1 foaf:mbox ?mbox1 . FILTER ( ?mbox1 = ?mbox2 && ?name1 != ?name2 )}
EOT
,
        array(
            'type'      => 'equation',
            'operator'  => '&&',
            'operand1'  => array(
                'type'      => 'equation',
                'operator'  => '=',
                'operand1'  => array('type' => 'value', 'value' => '?mbox1', 'quoted' => false),
                'operand2'  => array('type' => 'value', 'value' => '?mbox2', 'quoted' => false)
            ),
            'operand2'  => array(
                'type'      => 'equation',
                'operator'  => '!=',
                'operand1'  => array('type' => 'value', 'value' => '?name1', 'quoted' => false),
                'operand2'  => array('type' => 'value', 'value' => '?name2', 'quoted' => false)
            )
        )
    ),

    array(
<<<EOT
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
SELECT ?mbox1
{ ?name1 foaf:mbox ?mbox1 . FILTER regex(str(?mbox), "@work.example")}
EOT
,
        array(
            'type'      => 'function',
            'name'      => 'regex',
            'parameter' => array(
                array(
                    'type'      => 'function',
                    'name'      => 'str',
                    'parameter' => array(
                        array('type' => 'value', 'value' => '?mbox', 'quoted' => false)
                    )
                ),
                array(
                    'type'      => 'value',
                    'value'     => '@work.example',
                    'quoted'    => true
                )
            ),
        )
    ),

    array(
<<<EOT
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
SELECT ?mbox1
{ ?name1 foaf:mbox ?mbox1 . FILTER str(str(str(str(?mbox))))}
EOT
,
        array(
            'type'      => 'function',
            'name'      => 'str',
            'parameter' => array(
                array(
                    'type'      => 'function',
                    'name'      => 'str',
                    'parameter' => array(
                        array(
                            'type'      => 'function',
                            'name'      => 'str',
                            'parameter' => array(
                                array(
                                    'type'      => 'function',
                                    'name'      => 'str',
                                    'parameter' => array(
                                        array(
                                            'type'      => 'value',
                                            'value'     => '?mbox',
                                            'quoted'    => false
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    ),

    array(
<<<EOT
PREFIX  xsd: <http://www.w3.org/2001/XMLSchema#>
PREFIX  : <http://example.org/things#>
SELECT  ?x
WHERE
    { ?x :p ?v .
      FILTER (?v = 1.0)
    }
EOT
,
        array(
            'type'      => 'equation',
            'operator'  => '=',
            'operand1'  => array('type' => 'value', 'value' => '?v', 'quoted' => false),
            'operand2'  => array('type' => 'value', 'value' => '1.0', 'quoted' => false),
        )
    ),

    array(
<<<EOT
PREFIX  xsd: <http://www.w3.org/2001/XMLSchema#>
PREFIX  : <http://example.org/ns#>
SELECT  ?a
WHERE
    { ?a :p ?v .
      FILTER (?v) .
    }
EOT
,
        array(
            'type'      => 'value',
            'value'     => '?v',
            'quoted'    => false
        )
    ),

    array(
<<<EOT
PREFIX a:      <http://www.w3.org/2000/10/annotation-ns#>
PREFIX dc:     <http://purl.org/dc/elements/1.1/>
PREFIX xsd:    <http://www.w3.org/2001/XMLSchema#>

SELECT ?annot
WHERE { ?annot  a:annotates  <http://www.w3.org/TR/rdf-sparql-query/> .
        ?annot  dc:created   ?date .
        FILTER ( xsd:dateTime(?date) < xsd:dateTime("2005-01-01T00:00:00Z") )
      }
EOT
,
        array(
            'type'      => 'equation',
            'operator'  => '<',
            'operand1'  => array(
                    'type'      => 'function',
                    'name'      => 'xsd:dateTime',
                    'parameter' => array(
                        array('type' => 'value', 'value' => '?date', 'quoted' => false),
                    )
            ),
            'operand2'  => array(
                    'type'      => 'function',
                    'name'      => 'xsd:dateTime',
                    'parameter' => array(
                        array(
                            'type'      => 'value',
                            'value'     => '2005-01-01T00:00:00Z',
                            'quoted'    => true
                        )
                    )
            )
        )
    ),

    array(
<<<EOT
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>

SELECT ?p, ?o
  { ?s ?p ?o . FILTER (!!!isLiteral(?o)). }
EOT
,
        array(
            'negated'   => true,
            'type'      => 'function',
            'name'      => 'isLiteral',
            'parameter' => array(
                array('type' => 'value', 'value' => '?o', 'quoted' => false)
            ),
        )
    ),

    array(
<<<EOT
PREFIX  xsd: <http://www.w3.org/2001/XMLSchema#>
PREFIX  : <http://example.org/ns#>
SELECT  ?a
WHERE   { ?a :p ?v . FILTER ( ! ?v ) . }
EOT
,
        array(
            'negated'   => true,
            'type'      => 'value',
            'value'     => '?v',
            'quoted'    => false
        )
    ),
/**/
    array(
<<<EOT
PREFIX  xsd: <http://www.w3.org/2001/XMLSchema#>
PREFIX  : <http://example.org/ns#>
SELECT  ?a
WHERE { ?a :p ?v . FILTER ("true"^^xsd:boolean && ?v) .}
EOT
,
        array(
            'type'      => 'equation',
            'operator'  => '&&',
            'operand1'  => array(
                'type'      => 'value',
                'value'     => 'true',
                'quoted'    => true,
                'datatype'  => 'http://www.w3.org/2001/XMLSchema#boolean'
            ),
            'operand2'  => array('type' => 'value', 'value' => '?v', 'quoted' => false),
        )
    ),
    array(
<<<EOT
SELECT  *
WHERE
    { ?x ?y ?z .
      FILTER ( str(?z) = str("value"@en) ) .
    }
EOT
,
        array(
            'type'      => 'equation',
            'operator'  => '=',
            'operand1'  => array(
                'type'      => 'function',
                'name'      => 'str',
                'parameter' => array(
                    array(
                        'type'      => 'value',
                        'value'     => '?z',
                        'quoted'    => false
                    )
                )
            ),
            'operand2'  => array(
                'type'      => 'function',
                'name'      => 'str',
                'parameter' => array(
                    array(
                        'type'      => 'value',
                        'value'     => 'value',
                        'quoted'    => true,
                        'language'  => 'en'
                    )
                )
            )
        )
    ),
    array(
<<<EOT
SELECT  * WHERE
    { ?x ?y ?z .
      FILTER ( str(?x) = str(<http://rdf.hp.com/r-1>) ) .
    }
EOT
,
        array(
            'type'      => 'equation',
            'operator'  => '=',
            'operand1'  => array(
                'type'      => 'function',
                'name'      => 'str',
                'parameter' => array(
                    array(
                        'type'      => 'value',
                        'value'     => '?x',
                        'quoted'    => false
                    )
                )
            ),
            'operand2'  => array(
                'type'      => 'function',
                'name'      => 'str',
                'parameter' => array(
                    array(
                        'type'      => 'value',
                        'value'     => '<http://rdf.hp.com/r-1>',
                        'quoted'    => false
                    )
                )
            )
        )
    ),
    array(
<<<EOT
SELECT DISTINCT ?Res
WHERE {
?Res rdf:type ?_v1 FILTER (?Res = <ldap://ldap.seerose.biz/dc=biz,dc=seerose,ou=People>)
}
ORDER BY ?Res
EOT
,
        array(
            'type'      => 'equation',
            'operator'  => '=',
            'operand1'  => array('type' => 'value', 'value' => '?Res', 'quoted' => false),
            'operand2'  => array(
                'type'  => 'value',
                'value' => '<ldap://ldap.seerose.biz/dc=biz,dc=seerose,ou=People>',
                'quoted'=> false
            )
        )
    ),
);
?>
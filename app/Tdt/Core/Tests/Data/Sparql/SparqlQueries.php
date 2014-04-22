<?php

namespace Tdt\Core\Tests\Data\Sparql;

/**
 * Class containing SPARQL queries that are used in the unittesting.
 *
 * @author Jan Vansteenlandt jan@okfn.be
 */
class SparqlQueries
{

    public static $queries = array(
        // "countries" => "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
        // PREFIX type: <http://dbpedia.org/class/yago/>
        // PREFIX prop: <http://dbpedia.org/property/>
        // SELECT ?country_name ?population
        // WHERE {
        //     ?country a type:LandlockedCountries ;
        //     rdfs:label ?country_name ;
        //     prop:populationEstimate ?population .
        //     FILTER (?population > 15000000) .
        // }",

        // "countries2" => "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
        // PREFIX type: <http://dbpedia.org/class/yago/>
        // PREFIX prop: <http://dbpedia.org/property/>
        // SELECT ?country_name ?population
        // {
        //     ?country a type:LandlockedCountries ;
        //     rdfs:label ?country_name ;
        //     prop:populationEstimate ?population .
        //     FILTER (?population > 15000000) .
        // }",
        // // Search for smaller queries, these pass, but sometimes time out because of the size of the result
        // "vcard2" => "PREFIX vCard: <http://www.w3.org/2001/vcard-rdf/3.0#>
        // PREFIX foaf: <http://xmlns.com/foaf/0.1/>
        // CONSTRUCT { ?X vCard:FN ?name .
        //     ?X vCard:URL ?url .
        //     ?X vCard:TITLE ?title . }

        // FROM <http://dig.csail.mit.edu/2008/webdav/timbl/foaf.rdf>
        // {
        //     OPTIONAL { ?X foaf:name ?name . FILTER isLiteral(?name) . }
        //     OPTIONAL { ?X foaf:homepage ?url . FILTER isURI(?url) . }
        //     OPTIONAL { ?X foaf:title ?title . FILTER isLiteral(?title) . }
        // }",

        // "drugs" => "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
        // PREFIX db: <http://bio2rdf.org/drugbank_vocabulary:>
        // SELECT ?drug_name ?dosage ?indication
        // WHERE {
        //     ?drug a db:Drug .
        //     ?drug rdfs:label ?drug_name .
        //     OPTIONAL { ?drug db:dosage ?dosage . }
        //     OPTIONAL { ?drug db:indication ?indication . }
        // }",
    );
}

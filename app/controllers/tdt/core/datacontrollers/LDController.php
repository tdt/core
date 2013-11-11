<?php

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;

/**
* LD Controller
* @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
* @license AGPLv3
* @author Jan Vansteenlandt <jan@okfn.be>
* @author Michiel Vancoillie <michiel@okfn.be>
*/
class LDController extends SPARQLController {


    /**
     * We create a publication of the linked data within a graph that matches the name of the request uri.
     * The graph that will be published is the one that is stored in the graph model after the loading of triples
     * using the input package.
     */
    public function readData($source_definition, $rest_parameters = array()){

        $uri = \Request::root();

        // Get the definition of this ld defintion to retrieve the collection and resource name.
        $definition = $source_definition->definition()->first();

        $collection = $definition->collection_uri;
        $resource_name = $definition->resource_name;

        // Construct the graph name
        $graph_name = $uri . '/' . $collection . "/" .$resource_name;

        $graph = \Graph::whereRaw('graph_name like ?', array($graph_name))->first();

        // If no graph exists, abort the process
        if(empty($graph)){
            \App::abort('452', 'No graph entry identified by the name ' . $graph_name . ' has been found.');
        }


        echo "done";
        exit();
        $uri = $graph . $otherpart;

        $configObject->query = "SELECT count(?s) AS ?count WHERE{ GRAPH <$graph> { ?s ?p ?o . FILTER ( (?s LIKE '$uri') OR (?s LIKE '$uri/%') )}}";
        $count_obj = parent::read($configObject,$collection,$resource_name);
        $triples = $count_obj->triples;

        // Get the results#value, in order to get a count of all the results.
        // This will be used for paging purposes.
        $count = 0;
        foreach ($triples as $triple){
            if(!empty($triple['p']) && preg_match('/.*sparql-results#value/',$triple['p'])){
                $count = $triple['o'];
            }
        }

        // Calculate page link headers, previous and next.
        if($this->page > 1){
            $this->setLinkHeader($this->page-1, $this->page_size, "previous");
        }

        if($this->limit + $this->offset < $count){
            $this->setLinkHeader($this->page+1, $this->page_size, "next");

            $last_page = round($count / $this->limit,0);
            if($last_page > $this->page+1){
                $this->setLinkHeader($last_page,$this->limit, "last");
            }
        }

        $configObject->query = "CONSTRUCT { ?s ?p ?o } ";
        $configObject->query .= "WHERE { GRAPH <$graph> { ";
        $configObject->query .= "?s ?p ?o .";
        $configObject->query .= "FILTER ( (?s LIKE '$uri') OR (?s LIKE '$uri/%') )";
        $configObject->query .= "}  } ORDER BY asc(?s) OFFSET $this->offset LIMIT $this->limit";
        $configObject->isPaged = true;

        return parent::read($configObject, $collection, $resource);
    }
}

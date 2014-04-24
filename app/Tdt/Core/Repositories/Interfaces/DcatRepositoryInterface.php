<?php

namespace Tdt\Core\Repositories\Interfaces;

interface DcatRepositoryInterface
{

    /**
     * Return a DCAT document based on the definitions that are passed
     *
     * @param array Array with definition configurations
     * @param array Oldest definition, used to put a timestamp on the DCAT
     *
     * @return \EasyRdf_Graph
     */
    public function getDcatDocument(array $definitions, $oldest_definition);

    /**
     * Return the used namespaces in the DCAT document
     *
     * @return array
     */
    public function getNamespaces();
}

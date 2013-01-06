<?php

/**
 * Description of semanticsitemap
 *
 * @author Miel Vander Sande
 */

R::setup(Config::get("db", "system") . ":host=" . Config::get("db", "host") . ";dbname=" . Config::get("db", "name"), Config::get("db", "user"), Config::get("db", "password"));

$doc = ResourcesModel::getInstance()->getAllDoc();
header("content-type: application/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
echo "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9' xmlns:sc='http://sw.deri.org/2007/07/sitemapextension/scschema.xsd'>";
foreach ($doc as $package => $resources) {
    echo "<sc:dataset>";
    echo "<sc:datasetLabel>" . $package . "</sc:datasetLabel>";
    echo "<sc:datasetURI>" . Config::get("general","hostname") . Config::get("general","subdir") . $package . "</sc:datasetURI>";
    //echo "\t\t<sc:linkedDataPrefix slicing=''>" . Config::get("general","hostname") . Config::get("general","subdir") . $package . "</sc:linkedDataPrefix>\n";
    foreach ($resources as $resource => $val) {
        if ($resource != 'creation_date') {
            if (property_exists($val, 'requiredparameters')) {
                if (count($val->requiredparameters) == 0)
                    echo "<sc:dataDumpLocation>" . Config::get("general","hostname") . Config::get("general","subdir") . $package ."/" . $resource . ".nt" . "</sc:dataDumpLocation>";
            }
        } else {
            $dt = new DateTime();
            $dt->setTimestamp($val);
            echo "<lastmod>".$dt->format('Y-m-d\TH:i:s') . "</lastmod>";
        }
    }

    echo "<sc:sparqlEndpointLocation></sc:sparqlEndpointLocation>";
    echo "<changefreq>monthly</changefreq>\n";
    echo "</sc:dataset>";
}
echo "</urlset>"
?>

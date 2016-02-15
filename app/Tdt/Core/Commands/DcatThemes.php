<?php

namespace Tdt\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;
use Tdt\Core\Commands\Ie\Definitions;
use Tdt\Core\Commands\Ie\Users;
use Tdt\Core\Commands\Ie\Groups;
use EasyRdf\Graph;
use EasyRdf\Exception;

class DcatThemes extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datatank:themes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the themes used for DCAT with new ones, based on a taxonomy URI.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * Ask the user which functionality he wants to use (definitions|users)
     * then based on the input, proceed with the logic for chosen option.
     *
     * This option is given as an input argument upon making the command.
     *
     * @return void
     */
    public function fire()
    {
        $this->seedThemes();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('uri', InputArgument::REQUIRED, 'The URI of the document that holds the taxonomy that lists themes, themes will be searched on the
                basis of a skos:inScheme relationship and require a label.'),
            array('taxonomy_uri', InputArgument::OPTIONAL, 'The URI that points to the taxonomy inside the document.', ''),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
        );
    }

    /**
     * Seed the themes
     *
     * return @void
     */
    private function seedThemes()
    {
        $this->info('---- Seeding new themes ----');

        $base_uri = $this->argument('uri');

        $taxonomy_uri = $this->argument('taxonomy_uri');

        if (empty($taxonomy_uri)) {
            $taxonomy_uri = $base_uri;
        }

        // Try to get the themes from the ns.thedatatank.com (semantic data)
        try {
            $this->info('Trying to fetch triples from the uri: ' . $base_uri);

            $themes_graph = Graph::newAndLoad($base_uri);

            if ($themes_graph->isEmpty()) {
                $this->info('We could not reach the online themes.');

            } else {
                $this->info('Found new themes online, removing the old ones.');

                // Empty the themes table
                \Theme::truncate();
            }

            // Fetch the resources with a skos:conceptScheme relationship
            $resources = $themes_graph->allOfType('skos:ConceptScheme');

            $taxonomy_uris = array();

            foreach ($resources as $r) {
                array_push($taxonomy_uris, $r->getUri());
            }

            if (!empty($taxonomy_uris)) {
                if (count($taxonomy_uris) == 1) {
                    $taxonomy_uri = $taxonomy_uris[0];
                } else {
                    // Check if one of the possible taxonomy uris compares to the uri of the document
                    foreach ($taxonomy_uris as $tax_uri) {
                        if ($base_uri == $tax_uri) {
                            $taxonomy_uri = $tax_uri;
                            break;
                        }

                        $this->error('None of the URIs that have the skos:ConceptScheme property matched the URI of the document, please specify the taxonomy URI as a second parameter.');
                    }
                }
            } else {
                $this->error('No resource has been found with a property of skos:ConceptScheme.');
            }

            // Fetch all of the themes
            foreach ($themes_graph->resourcesMatching('skos:inScheme') as $theme) {
                if ($theme->get('skos:inScheme')->getUri() == $taxonomy_uri) {
                    $uri = $theme->getUri();

                    $label = $theme->getLiteral('rdfs:label');

                    if (!empty($label) && !empty($uri)) {
                        $label = $label->getValue();

                        $this->info('Added ' . $uri . ' with label ' . $label);

                        \Theme::create(array(
                            'uri' => $uri,
                            'label' => $label
                            ));
                    }
                }
            }

            $this->info('Added new themes.');

        } catch (Exception $ex) {
            $this->info('An error occurred when we tried to fetch online themes.');
        }
    }
}

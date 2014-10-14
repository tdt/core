<?php

namespace Tdt\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;
use Tdt\Core\Commands\Ie\Definitions;
use Tdt\Core\Commands\Ie\Users;
use Tdt\Core\Commands\Ie\Groups;

class DcatThemes extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datatank:theme';

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
            array('uri', InputArgument::REQUIRED, 'The URI of the taxonomy that lists themes, themes will be searched on the
                basis of a skos:inScheme relationship and require a label.'),
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

        // Try to get the themes from the ns.thedatatank.com (semantic data)
        try {

            $this->info('Trying to fetch triples from the uri: ' . $base_uri);

            $themes_graph = \EasyRdf_Graph::newAndLoad($base_uri);

            if ($themes_graph->isEmpty()) {

                $this->info('We could not reach the online themes.');

            } else {

                $this->info('Found new themes online, removing the old ones.');

                // Empty the themes table
                \Theme::truncate();
            }

            // Fetch all of the themes
            foreach ($themes_graph->resources('skos:inScheme', $base_uri . '#Taxonomy') as $theme) {

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

            $this->info('Added new themes.');

        } catch (EasyRdf_Exception $ex) {
            $this->info('An error occurred when we tried to fetch online themes.');
        }
    }
}

<?php

namespace Tdt\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;
use Tdt\Core\Commands\Ie\Definitions;
use Tdt\Core\Commands\Ie\Users;
use Tdt\Core\Commands\Ie\Groups;

class DcatLicenses extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datatank:licenses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the licenses for the datasets. By default it will install a set of internationally known licenses.';

    /**
     * The URI for the default licenses
     *
     * @var string
     */
    protected $uri_licenses = 'https://raw.githubusercontent.com/tdt/licenses/master/international_licenses.json';

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
        $this->seedLicenses();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::OPTIONAL, 'The name of the licenses document on github.com/tdt/licenses that should be seeded into the datatank.', ''),
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
    private function seedLicenses()
    {
        $this->info('---- Seeding new licenses ----');

        // TODO check if argument is given, use default licenses

        $uri_licenses = $this->uri_licenses;

        // Try to get the themes from the ns.thedatatank.com (semantic data)
        try {
            $this->info('Trying to fetch triples from the uri: ' . $uri_licenses);

            $themes_graph = \EasyRdf_Graph::newAndLoad($uri_licenses, 'jsonld');

            if ($themes_graph->isEmpty()) {
                $this->info('We could not reach the online themes.');

            } else {
                $this->info('Found new themes online, removing the old ones.');

                // Empty the themes table
                \License::truncate();
            }

            // Fetch the resources with a skos:conceptScheme relationship
            $licenses = $themes_graph->allOfType('cc:License');

            $taxonomy_uris = array();

            foreach ($licenses as $license) {
                $url = '';
                $title = '';
                $identifier = '';

                $title_resource = $license->getLiteral('dc:title');
                $identifier_resource = $license->getLiteral('dc:identifier');

                if (!empty($title_resource)) {
                    $title = $title_resource->getValue();
                }

                if (!empty($identifier_resource)) {
                    $identifier = $identifier_resource->getValue();
                }

                if (!$license->isBNode()) {
                    $url = $license->getUri();
                }

                \License::create([
                    'url' => $url,
                    'title' => $title,
                    'license_id' => $identifier
                ]);

                $this->info('Added license "' . $identifier . '" with title "' . $title . '" and URI ' . $url);
            }

        } catch (EasyRdf_Exception $ex) {
            $this->info('An error occurred when we tried to fetch online themes.');
        }
    }
}

<?php

namespace Tdt\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;
use Tdt\Core\Commands\Ie\Definitions;
use Tdt\Core\Commands\Ie\Users;
use Tdt\Core\Commands\Ie\Groups;

class GeoProjections extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datatank:geoprojections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reseed the geoprojections or add one through the command line.';

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
        $option = $this->ask("What do you want to do (pass number). (1) Reseed the geo projections (2) Add a geo projection ");

        if ($option == 1) {
            $this->seedGeoprojections();
        } elseif ($option == 2) {
            $epsg = $this->ask("Enter the EPSG code: ");
            $projection = $this->ask("Enter the projection string (in OGC WKT format): ");

            \Geoprojection::create([
                'epsg' => $epsg,
                'projection' => $projection
            ]);
        } else {
            $this->error("Option $option is not supported.");
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
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
    private function seedGeoprojections()
    {
        $this->info('---- Geo projections ----');

        $this->info('Fetching geoprojections from the local json file.');

        $geoprojections = json_decode(file_get_contents(app_path() . '/database/seeds/data/geoprojections.json'));

        if (!empty($geoprojections)) {
            $this->info('Geoprojections have been found, deleting the current ones, and replacing them with the new ones.');

            \Geoprojection::truncate();

            foreach ($geoprojections as $language) {
                \Geoprojection::create(array(
                    'epsg' => $language->epsg,
                    'projection' =>$language->projection,
                ));
            }

            $this->info('Added the geoprojections from a local json file.');

        } else {
            $this->info('No geoprojections have not been found in the file, the old ones will not be replaced.');
        }
    }
}

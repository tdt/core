<?php

namespace Tdt\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Filesystem\Filesystem;

use Tdt\Core\Commands\Ie\Definitions;
use Tdt\Core\Commands\Ie\Users;
use Tdt\Core\Commands\Ie\Groups;

class Import extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datatank:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import functionality for a datatank configuration, can import users and resources passed in a JSON file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->definitions = new Definitions();
        $this->groups = new Groups();
        $this->users = new Users();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

        // Fetch the json file from the arguments
        $file = $this->argument('file');

        // Check if the file exists
        if (\File::exists($file)) {

            // JSON decode
            $content = json_decode(\File::get($file), true);

            if ($content) {

                // Check for user & groups
                if(!empty($content['users']) && is_array($content['users'])
                    && !empty($content['groups']) && is_array($content['groups'])){

                    $this->info("\n——————————————————————————————————");
                    $this->info("Users & groups found, importing...");
                    $this->info("——————————————————————————————————");

                    // Import groups
                    $messages = $this->groups->import($content['groups']);

                    foreach ($messages as $group => $status) {
                        if ($status) {
                            $this->info("• Group '$group' succesfully added.");
                        } else {
                            $this->error("• Group '$group' already existed, ignored.");
                        }
                    }

                    // Import users
                    $messages = $this->users->import($content['users']);

                    foreach ($messages as $user => $status) {
                        if ($status) {
                            $this->info("• User '$user' succesfully added.");
                        } else {
                            $this->error("• User '$user' already existed, ignored.");
                        }
                    }
                }

                // Check for definitions
                if (!empty($content['definitions']) && is_array($content['definitions'])) {

                    $this->info("———————————————————————————————");
                    $this->info("Definitions found, importing...");
                    $this->info("———————————————————————————————");

                    // Ask for a set of credentials that have definition-create permissions
                    $username = $this->ask("Enter a username that has permission to create definitions: ");
                    $password = $this->secret("Enter the corresponding password: ");

                    $data = array();
                    $data['username'] = $username;
                    $data['password'] = $password;
                    $data['definitions'] = $content['definitions'];

                    $messages = $this->definitions->import($data);

                    foreach ($messages as $identifier => $status) {
                        if ($status) {
                            $this->info("• Definition with '$identifier' succesfully added.");
                        } else {
                            $this->error("Something went wrong when trying to adding the definition '$identifier', check the logs for indications of what may have gone wrong.");
                        }
                    }
                }

                $this->info("\nCompleted task");
            } else {
                $this->error("The given file doesn't contain valid JSON.");
                die();
            }
        } else {
            $this->error("The given file '$file' can't be found on the system.");
            die();
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
            array("file", InputArgument::REQUIRED, "The path to the JSON export file.", null),
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
}

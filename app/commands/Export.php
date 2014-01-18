<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use tdt\core\definitions\DefinitionController;

class Export extends Command {

    /**
     * The default filename to export definitions to.
     *
     * @var string
     */
    protected $definition_file = "definition_export.json";

    /**
     * The default filename to export users to.
     *
     * @var string
     */
    protected $user_file = "user_export.json";

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'core:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export functionality for the definition and user configuration. By default it exports the all of the definitions.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Assign the command directory to be the default directory to put the
        // export definitions and users in
        $this->definition_file = app_path() . "/commands/" . $this->definition_file;
        $this->users_file = app_path() . "/commands/" . $this->user_file;
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

        $filename = $this->option('file');

        // Check which export functionality is chosen,
        // by default opt to export definitions
        if($this->option('users')){

            $this->error("Not yet implemented.");
            die;

            // Ask if all of the user need to be exported or a specific one
            if($this->confirm("Do you want to export all of the users? [yes|no]")){

                $users = \Sentry::findAllUsers();

            }else{

                // Ask for the name of a specific user
                $users = "";
            }

        }else{

            $file_content = "";

            // Ask if all definitions can be exported or only a specific one
            if($this->confirm("Do you want to export all of the definitions? [yes|no]")){

                // Request all of the definitions
                $definitions = DefinitionController::getAllDefinitions();
                $file_content = str_replace('\/', '/', json_encode($definitions));

            }else{

                $resource_id = $this->ask('Provide the full identifier of the definition you want (e.g. foo/foobar/bar): ');

                // Check if the resource id is legit
                $definition = DefinitionController::get($resource_id);

                // If the resource_id doesn't check out, give the user another chance to pass it along
                while(empty($definition)){

                    $this->error("The given resource identifier ($resource_id) could not be found.");

                    $resource_id = $this->ask('Provide the full identifier of the definition you want (e.g. foo/foobar/bar): ');

                    // Check if the resource id is legit
                    $definition = DefinitionController::get($resource_id);
                }

                $def_props = $definition->getAllParameters();

                // These definition properties are put into a single array
                // we still need to map it to its corresponding identifier
                $def_props = array($resource_id => $def_props);
                $file_content = str_replace('\/', '/', json_encode($def_props));

            }

            // If the fetched filename was empty, assign the default file name
            // and the default path
            if(empty($filename)){
                $filename = $this->definition_file;
            }

            try{
                file_put_contents($filename, $file_content);
            }catch(Exception $e){
                $this->error("The contents could not be written to the file ($filename).");
            }

            $this->info("The export has been written to $filename.");
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
            array('users', 'u', InputOption::VALUE_NONE, 'Export the users', null),
            array('file', 'f', InputOption::VALUE_OPTIONAL, 'Write the export data to this file.', null),
        );
    }

}
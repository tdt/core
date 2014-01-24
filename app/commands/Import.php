<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Filesystem\Filesystem;
use tdt\core\definitions\DefinitionController;

class Import extends Command {

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
    protected $description = 'Import functionality for definition and user configuration. By default it assumes that the given json file is a definition configuration.';

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
     * @return void
     */
    public function fire()
    {

        if($this->option('users')){

            $this->error("Not yet implemented.");
            die;

        }else{

            // Fetch the json file from the arguments
            $file = $this->argument('input');

            // Check if the file exists
            if(File::exists($file)){

                $content = json_decode(File::get($file), true);

                if($content){

                    // Ask for a set of credentials that have definition-create permissions
                    $username = $this->ask("Enter a username that has permission to create definitions: ");
                    $password = $this->secret("Enter the corresponding password: ");

                    $auth_header = "Basic " . base64_encode($username . ":" . $password);

                    foreach($content as $identifier => $definition_params){

                        $headers = array(
                                        'Content-Type' => 'application/tdt.definition+json',
                                        'Authorization' => $auth_header,
                                    );

                        $this->updateRequest('PUT', $headers, $definition_params);

                        // Add the new definition
                        $response = DefinitionController::handle($identifier);

                        $status_code = $response->getStatusCode();

                        if($status_code == 200){
                            $this->info("A new definition with identifier ($identifier) was succesfully added.");
                        }else{
                            $this->error("A status of $status_code was returned when adding $identifier, check the logs for indications of what may have gone wrong.");
                        }
                    }
                }else{
                    $this->error("The given file contents couldn't be json decoded.");
                    die;
                }
            }else{
                $this->error("The given file ($file) can't be found on the system.");
                die;
            }
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
            array("input", InputArgument::REQUIRED, "The absolute path to the json file.", null),
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
            array("users", "u" ,InputOption::VALUE_NONE, "Import users.", null),
        );
    }

    /**
     * Custom API call function
     */
    public function updateRequest($method, $headers = array(), $data = array()){

        // Set the custom headers.
        \Request::getFacadeRoot()->headers->replace($headers);

        // Set the custom method.
        \Request::setMethod($method);

        // Set the content body.
        if(is_array($data)){
            \Input::merge($data);
        }
    }

}
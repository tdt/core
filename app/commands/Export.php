<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use tdt\commands\ie\Definitions;
use tdt\commands\ie\Users;
use tdt\commands\ie\Groups;

class Export extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datatank:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export functionality for the datatank configuration. By default it exports all the data.';

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
        // Check for filename
        $filename = $this->option('file');

        // Get the optional identifier
        $identifier = $this->argument('identifier');

        // Create new object for the export data;
        $export_data = new \stdClass();


        if($this->option('definitions')){

            $export_data->definitions = Definitions::export($identifier);

            if(!empty($identifier)){

                // When the identifier doesn't check out, give the user another chance to fill it out
                while(empty($export_data->definitions)){

                    $this->error("The resource with identifier '$identifier' could not be found.");

                    // Prompt new indentifier
                    $identifier = $this->ask('Provide the full identifier of the definition you want (e.g. csv/cities): ');

                    // Check if the resource id is legit
                    $export_data->definitions = Definitions::export($identifier);
                }
            }

        }elseif($this->option('users')){

            // Export users
            $export_data->users = Users::export();
            // Export groups
            $export_data->groups = Groups::export();

        }else{

            // Export definitions
            $export_data->definitions = Definitions::export();

            // Export users
            $export_data->users = Users::export();
            // Export groups
            $export_data->groups = Groups::export();
        }


        // JSON encode
        if (defined('JSON_PRETTY_PRINT')) {
            $export_data = json_encode($export_data, JSON_PRETTY_PRINT);
        }else{
            $export_data = json_encode($export_data);
        }
        // JSON_UNESCAPED_SLASHES only available in PHP 5.4
        $export_data = str_replace('\/', '/', $export_data);


        // Output
        if(empty($filename)){
            // Print to console
            echo $export_data;
        }else{
            try{
                // Write to file
                file_put_contents($filename, $export_data);
                $this->info("The export has been written to the file '$filename'.");
            }catch(Exception $e){
                $this->error("The contents could not be written to the file '$filename'.");
                die();
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
            array('identifier', InputArgument::OPTIONAL, 'If you specify the option -d, you can export a single definition by specifying the indentifier of that definition (e.g. csv/cities)'),
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
            array('definitions', 'd', InputOption::VALUE_NONE, 'Only export the definitions, if you specify an identifier only that definition will be exported.', null),
            array('users', 'u', InputOption::VALUE_NONE, 'Only export the users.', null),
            array('file', 'f', InputOption::VALUE_OPTIONAL, 'Write the export data to a file.', null),
        );
    }

}
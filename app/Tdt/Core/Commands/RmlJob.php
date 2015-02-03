<?php

namespace Tdt\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RmlJob extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'rml:execute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute an RML job with a given configuration. (Pass the identifier that holds the configuration)';

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
     * The output file prefix where the RML will write its triples to
     */
    private $output_file_prefix = 'rmloutput_';

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
        // Trigger the RML command
        $def_repo = \App::make('Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface');
        $rml_repo = \App::make('Tdt\Core\Repositories\Interfaces\RmlDefinitionRepositoryInterface');

        $identifier = $this->argument('uri');

        $definition = $def_repo->getByIdentifier($identifier);

        // Check if it's an RML definition
        if (empty($definition) || $definition['source_type'] != 'RmlDefinition') {

            $this->error('The given identifier was not found or does not represent an RML definition.');
            die();
        }

        // Fetch the configuration of the RML definition
        $rml_definition = $rml_repo->getById($definition['source_id']);

        // Execute the mvn command that triggers the RML

        // Create the file path to write the triples to
        $file = $this->output_file_prefix . str_replace('/', '_', $identifier) . '.nt';

        $rml_home = \Config::get('input::rml.rml_home');

        $command = "cd $rml_home;mvn exec:java -Dexec.args=\"" . $rml_definition['mapping_document'] . " $file\"";

        $this->info("executing the following command(s): " . $command);
        $this->info(shell_exec($command));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('uri', InputArgument::REQUIRED, 'The URI of the configuration that holds the RML document location.'), // This could also be passed in the arguments of the command?
            array('output_file', InputArgument::OPTIONAL, 'The fully qualified path that will be used to write the triples to.
                If no path is given, it will default to the name of the identifier and will be placed inside of the RML project folder.')
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

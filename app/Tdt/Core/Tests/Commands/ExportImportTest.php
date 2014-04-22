<?php

namespace Tdt\Core\Tests\Commands;

use Tdt\Core\Tests\TestCase;
use Tdt\Core\Commands\Export;
use Tdt\Core\Commands\Ie\Definitions;
use Tdt\Core\Commands\Ie\Users;
use Tdt\Core\Commands\Ie\Groups;
use Illuminate\Console\Application;
use Illuminate\Filesystem\Filesystem;

class ExportImportTest extends TestCase
{

    /**
     * Test the export command on definitions
     */
    public function testExportDefinitions()
    {
        // Seed the database with definitions to export
        \Artisan::call('db:seed', array("--class" => "DemoDataSeeder"));

        // Call the export command and catch the output
        ob_start();

        $outputStream = new \Symfony\Component\Console\Output\StreamOutput(fopen('php://output', 'w'));

        \Artisan::call('datatank:export', array('--definitions' => 'definitions'), $outputStream);

        $export_body = json_decode(ob_get_clean(), true);

        $this->assertTrue(!empty($export_body));

        // Check for corresponding versions
        $version = \Config::get('app.version');

        $exported_version = @$export_body['version'];

        $this->assertTrue(!empty($exported_version));

        $this->assertEquals($exported_version, $version);

        // The demo data seeder has 5 definitions
        $export_definitions = @$export_body['definitions'];

        $this->assertTrue(!empty($export_definitions));

        $this->assertEquals(6, count($export_definitions));

        // Write the output to a file, necessary to be using in the import
        if (!empty($export_body)) {

            $fs = new Filesystem();

            $fs->put($this->getCommandTestFolder() . 'export_definitions.json', json_encode($export_body));
        }
    }

    /**
     * Text the export command on the users
     */
    public function testExportUsers()
    {
        // Call the users export command and catch the output
        $outputStream = new \Symfony\Component\Console\Output\StreamOutput(fopen('php://output', 'w'));

        ob_start();

        \Artisan::call('datatank:export', array('--users' => 'users'), $outputStream);

        $export_body = json_decode(ob_get_clean(), true);

        $this->assertTrue(!empty($export_body));

        // Check for corresponding versions
        $version = \Config::get('app.version');

        $exported_version = @$export_body['version'];

        $this->assertTrue(!empty($exported_version));

        $this->assertEquals($exported_version, $version);

        // The user seeder seeds 2 users
        $export_users = @$export_body['users'];

        $this->assertTrue(!empty($export_users));

        $this->assertEquals(2, count($export_users));

        // Write the output to a file, necessary to be using in the import
        if (!empty($export_body)) {

            $fs = new Filesystem();

            $fs->put($this->getCommandTestFolder() . 'export_users.json', json_encode($export_body));
        }
    }

    /**
     * Test the import command for the definitions
     */
    public function testImportDefinitions()
    {
        // Read the definitions json file and import
        $fs = new Filesystem();

        $json_definitions = $fs->get($this->getCommandTestFolder() . 'export_definitions.json');

        $definitions = json_decode($json_definitions, true);

        $this->assertTrue(!empty($definitions));

        // Call the users export command and catch the output
        $outputStream = new \Symfony\Component\Console\Output\StreamOutput(fopen('php://output', 'w'));

        // Instead of calling the import command use the functionality wrappers
        // (Definitions, Users, Groups) because the import requires prompts
        $definitions_wrapper = new Definitions();

        // Add default username and password
        $definitions['username'] = 'admin';
        $definitions['password'] = 'admin';

        $definitions_wrapper->import($definitions);
    }

    /**
     * Test the import command for the users
     */
    public function testImportUsers()
    {

        // Read the users json file and import
    }

    /**
     * Return the path to the folder that can be used to store the test files into
     */
    private function getCommandTestFolder()
    {
        return sys_get_temp_dir() . '/';
    }
}

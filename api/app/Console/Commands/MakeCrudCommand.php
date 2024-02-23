<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class MakeCrudCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create everything needed for a CRUD operation';

    /**
     * Filesystem instance
     *
     * @var \Illuminate\Filesystem\Filesystem $files
     */
    private $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {name} {--apiVersion=}';

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $file
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiVersion = $this->option('apiVersion') ?? 'v1';
        if (is_numeric($apiVersion)) {
            $apiVersion = 'v' . $apiVersion;
        }

        $name = $this->argument('name');

        Artisan::call('make:repository', [
            'name'          => $name . 'Repository', 
            '--apiVersion'  => $apiVersion,
            '--interface'   => 1, 
        ]);
        
        Artisan::call('make:service', [
            'name'          => $name . 'Service', 
            '--apiVersion'  => $apiVersion,
            '--interface'   => 1, 
        ]);

        Artisan::call('make:controller', [
            'name'          => $name . 'Controller', 
            '--api'         => 1, 
            '--apiVersion'  => $apiVersion,
            '--request'     => 1,
            '--resource'    => 1,
        ]);

        shell_exec('chmod -R 777 ' . base_path());
        $this->info('All CRUD resources were created successfully.');
    }
}

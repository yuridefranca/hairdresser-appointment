<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class MakeControllerCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class';

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
    protected $signature = 'make:controller {name} {--api} {--apiVersion=} {--request} {--resource}';

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
     * Return the stub file path
     *
     * @return string
     */
    public function getStubPath()
    {
        if ($this->option('api')) {
            return base_path('stubs/controller.api.stub');
        }

        return base_path('stubs/controller.stub');
    }

    /**
     * Return the service class file path
     *
     * @return string
     */
    public function getFilePath($name)
    {
        $basePath = base_path('app/Http/Controllers/');
        if ($this->option('api')) {
            $basePath .= 'Api' . '/' . $this->option('apiVersion') . '/';
        }

        return $basePath . $name . '.php';
    }

    /**
     * Return the service class file content
     *
     * @param string $name
     * @param string $apiVersion
     *
     * @return string
     */
    public function getFileContents($name)
    {
        $namespace = 'App\Http\Controllers';
        if ($this->option('api')) {
            $namespace .= '\Api' . '\\' . $this->option('apiVersion');
        }

        $content = file_get_contents($this->getStubPath());
        $content = str_replace('{{ apiVersion }}', $this->option('apiVersion'), $content);
        $content = str_replace('{{ className }}', $name, $content);
        $content = str_replace('{{ modelClassName }}', explode('Controller', $name)[0], $content);
        $content = str_replace('{{ namespace }}', $namespace, $content);

        if ($this->option('api')) {
            $content = str_replace('{{ resourceName }}', explode('controller', strtolower($name))[0], $content);
        }

        return $content;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @return string
     */
    protected function makeDirectory()
    {
        $path = explode($this->argument('name') . '.php', $this->getFilePath($this->argument('name')))[0];
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        $this->makeDirectory();

        if ($this->files->exists($this->getFilePath($name))) {
            return $this->error('Controller already exists!');
        }

        if ($this->option('request')) {
            Artisan::call('make:request', ['resource' => explode('Controller', $name)[0], '--api' => 1, '--apiVersion' => $this->option('apiVersion'), '--all' => 1]);
        }

        if ($this->option('resource')) {
            if ($this->option('api')) {
                Artisan::call('make:resource', ['name' => 'Api' . '/' . $this->option('apiVersion') . '/' . explode('Controller', $name)[0] . 'Resource']);
            } else {
                Artisan::call('make:resource', ['name' => explode('Controller', $name)[0] . 'Resource']);
            }
        }

        $this->files->put($this->getFilePath($name), $this->getFileContents($name));
        $this->info('Controller created successfully.');
    }
}

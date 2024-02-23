<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeRequestCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new request class';

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
    protected $signature = 'make:request {resource} {name?} {--api} {--apiVersion=} {--all}';

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
            return base_path('stubs/request.api.stub');
        }

        return base_path('stubs/request.stub');
    }

    /**
     * Return the service class file path
     *
     * @return string
     */
    public function getFilePath($filename)
    {
        $basePath = base_path('app/Http/Requests/');
        if ($this->option('api')) {
            $basePath .= 'Api' . '/' . $this->option('apiVersion') . '/';
        }

        return $basePath . ucfirst($this->argument('resource')) . '/' . $filename . '.php';
    }

    /**
     * Return the service class file content
     *
     * @param string $filename
     *
     * @return string
     */
    public function getFileContents($filename)
    {
        $namespace = 'App\Http\Requests';
        if ($this->option('api')) {
            $namespace .= '\Api' . '\\' . $this->option('apiVersion') . '\\' . ucfirst($this->argument('resource'));
        }

        $content = file_get_contents($this->getStubPath());
        $content = str_replace('{{ namespace }}', $namespace, $content);
        $content = str_replace('{{ baseRequestNamespace }}', str_replace(ucfirst($this->argument('resource')), 'BaseRequest', $namespace), $content);
        $content = str_replace('{{ className }}', $filename, $content);

        return $content;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param string $filename
     *
     * @return string
     */
    protected function makeDirectory($filename)
    {
        $path = explode($filename . '.php', $this->getFilePath($filename))[0];
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
        if (!$this->option('all')) {
            $filename = $this->argument('resource') . $this->argument('name');
            if ($this->files->exists($this->getFilePath($filename))) {
                return $this->error('Request already exists!');
            }

            $this->makeDirectory($filename);
            $this->files->put($this->getFilePath($filename), $this->getFileContents($filename));
        } else {
            $crudMethods = ['Index', 'Show', 'Store', 'Update', 'Destroy'];
            $filenames = array_map(function ($method) {
                return $this->argument('resource') . $method . 'Request';
            }, $crudMethods);

            foreach ($filenames as $filename) {
                if ($this->files->exists($this->getFilePath($filename))) {
                    return $this->error('Request already exists!');
                }

                $this->makeDirectory($filename);
                $this->files->put($this->getFilePath($filename), $this->getFileContents($filename));
            }
        }

        $this->info('Request created successfully.');
    }
}

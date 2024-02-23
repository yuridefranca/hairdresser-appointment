<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class MakeRepositoryCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

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
    protected $signature = 'make:repository {name} {--I|interface} {--apiVersion=}';

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
        return base_path('stubs/repository.stub');
    }

    /**
     * Return the repository class file path
     *
     * @param string $apiVersion
     * @param string $name
     * 
     * @return string
     */
    public function getFilePath($apiVersion, $name)
    {
        return base_path('app/Repositories/') . strtolower($apiVersion) . '/' . $name . '.php';
    }

    /**
     * Return the repository class file content
     *
     * @param string $apiVersion
     * @param string $name
     *
     * @return string
     */
    public function getFileContents($apiVersion, $name)
    {
        $content = file_get_contents($this->getStubPath());
        $content = str_replace('{{ className }}', $name, $content);
        $content = str_replace('{{ modelClassName }}', explode('Repository', $name)[0], $content);
        $content = str_replace('{{ namespace }}', 'App\Repositories\\' . $apiVersion, $content);
        $content = str_replace('{{ interfaceNamespace }}', 'App\Repositories\Interfaces\\' . $apiVersion, $content);

        return $content;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param string $apiVersion
     * 
     * @return string
     */
    protected function makeDirectory($apiVersion)
    {
        $path = base_path('app/Repositories/' . $apiVersion);
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Register the repository on the provider
     * 
     * @param string $apiVersion
     * @param string $name
     * 
     * @return void
     */
    protected function registerOnProvider($apiVersion, $name)
    {
        $path = base_path('app/Providers/RepositoryProvider.php');
        $content = file_get_contents($path);

        $functionName = 'register';
        
        // add use statement to import the repository
        $classToImport = 'App\Repositories\\' . $apiVersion . '\\' . $name;
        $pattern = "/(use\s+[^\s]+\s*;\s*)(.*?)/s";
        $replacement = "$1use " . $classToImport . ";\n$2";
        $content = preg_replace($pattern, $replacement, $content, 1);

        // add use statement to import the interface
        $interfaceToImport = 'App\Repositories\Interfaces\\' . $apiVersion . '\\' . $name . 'Interface';
        $pattern = "/(use\s+[^\s]+\s*;\s*)(.*?)/s";
        $replacement = "$1use " . $interfaceToImport . ";\n$2";
        $content = preg_replace($pattern, $replacement, $content, 1);

        // add bind statement to register the repository
        $lineToAdd = '        $this->app->bind(' . $name . 'Interface::class, ' . $name . '::class);';
        $pattern = "/(function\s+" . preg_quote($functionName, '/') . "\s*\([^\)]*\)\s*\{)(.*?)(\n\s*\})/s";
        $replacement = "$1$2\n" . $lineToAdd . "$3";
        $content = preg_replace($pattern, $replacement, $content);

        file_put_contents($path, $content);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $apiVersion = $this->option('apiVersion') ?? 'v1';
        if (is_numeric($apiVersion)) {
            $apiVersion = 'v' . $apiVersion;
        }

        $name = $this->argument('name');

        $path = $this->makeDirectory($apiVersion);
        if ($this->files->exists($this->getFilePath($apiVersion, $name))) {
            return $this->error('Repository already exists!');
        }

        if ($this->option('interface')) {
            Artisan::call('make:interface', ['name' => $name . 'Interface', '--repository' => 1, '--apiVersion' => $apiVersion]);
        }

        $this->files->put($this->getFilePath($apiVersion, $name), $this->getFileContents($apiVersion, $name));

        shell_exec('chmod -R 777 ' . $path);
        $this->registerOnProvider($apiVersion, $name);
        $this->info('Repository created successfully.');
    }
}

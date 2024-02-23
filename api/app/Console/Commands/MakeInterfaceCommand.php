<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeInterfaceCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new interface';

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
    protected $signature = 'make:interface {name} {--apiVersion=} {--S|service} {--R|repository}';

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
     * @param string $type
     *
     * @return string
     */
    public function getStubPath($type)
    {

        if ($type === 'repository') {
            return base_path('stubs/repository.interface.stub');
        }

        return base_path('stubs/service.interface.stub');
    }

    /**
     * Return the interface file path
     *
     * @param string $apiVersion
     * @param string $name
     * @param string $type
     *
     * @return string
     */
    public function getFilePath($apiVersion, $name, $type)
    {
        if ($type === 'repository') {
            return base_path('app/Repositories/Interfaces/') . strtolower($apiVersion) . '/' . $name . '.php';
        }

        return base_path('app/Services/Interfaces/') . strtolower($apiVersion) . '/' . $name . '.php';
    }

    /**
     * Return the interface file content
     *
     * @param string $apiVersion
     * @param string $name
     * @param string $type
     *
     * @return string
     */
    public function getFileContents($apiVersion, $name, $type)
    {
        $layer = $type === 'repository' ? 'Repositories' : 'Services';
        $content = file_get_contents($this->getStubPath($type));
        $content = str_replace('{{ interfaceName }}', $name, $content);
        $content = str_replace('{{ namespace }}', 'App\\' . $layer . '\Interfaces\\' . $apiVersion, $content);

        return $content;
    }

    /**
     * Build the directory for the interface if necessary.
     *
     * @param string $apiVersion
     * @param string $type
     *
     * @return string
     */
    protected function makeDirectory($apiVersion, $type)
    {
        $path = $type === 'repository' ?
            base_path('app/Repositories/Interfaces/' . $apiVersion) :
            base_path('app/Services/Interfaces/' . $apiVersion);

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
        $apiVersion = $this->option('apiVersion') ?? 'v1';
        if (is_numeric($apiVersion)) {
            $apiVersion = 'v' . $apiVersion;
        }

        $name = $this->argument('name');

        if ($this->option('repository') || str_contains($name, 'Repository')) {
            $path = $this->makeDirectory($apiVersion, 'repository');
            if ($this->files->exists($this->getFilePath($apiVersion, $name, 'repository'))) {
                return $this->error('Repository already exists!');
            }

            $this->files->put($this->getFilePath($apiVersion, $name, 'repository'), $this->getFileContents($apiVersion, $name, 'repository'));
            shell_exec('chmod -R 777 ' . $path);
            $this->info('Repository Interface created successfully.');
            return;
        }

        $path = $this->makeDirectory($apiVersion, 'service');
        if ($this->files->exists($this->getFilePath($apiVersion, $name, 'service'))) {
            return $this->error('Service already exists!');
        }

        $this->files->put($this->getFilePath($apiVersion, $name, 'service'), $this->getFileContents($apiVersion, $name, 'service'));
        shell_exec('chmod -R 777 ' . $path);
        $this->info('Service Interface created successfully.');
    }
}

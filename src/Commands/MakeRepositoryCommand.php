<?php

namespace Cuongnd88\LaraRepo\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class MakeRepositoryCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {--interface= : Generate the Interface file} {--repository= : Generate the Repository file} {--model= : Allocate or Create the Model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the Interface and Repository files';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    protected $defaultRepositoryNamespace = 'Repositories';

    protected $baseRepositoryInterface = 'RepositoryInterface';

    protected $baseRepository = 'BaseRepository';

    protected $interfaceInput, $repositoryInput, $modelInput;

    protected $interfaceClass, $repositoryClass, $modelClass;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->getOptionsInput();

            $this->checkOptionsInput();;

            $this->makeRepositoryDirectory();

            $this->createBaseInterface();

            $this->createBaseRepository();

            $this->createInterface();

            $this->allocateOrCreateModel();

            $this->createRepository();

            $this->mergeRepositoryConfig();

            $this->info($this->type.' created successfully.');
        } catch (\Exception $e) {
            report($e);
            $this->error($e->getMessage());
        }
    }

    /**
     * Allocate or Create model if not exist
     *
     * @param string $model
     *
     * @return void
     */
    public function allocateOrCreateModel($model = null)
    {
        $model = $model ?? $this->modelInput;
        $name = $this->qualifyClass($model);
        $path = $this->getPath($name);

        if ($this->files->missing($path) && $this->confirm('This model is not existed. Do you wish to create?')) {
            Artisan::call('make:model', ['name' => $this->modelInput]);
        }

        $this->modelClass = '\\'.$name;
    }

    /**
     * Merge repository items and create config file
     *
     * @return void
     */
    public function mergeRepositoryConfig()
    {
        $filePath = config_path('repositories.php');
        $content = config('repositories');

        if ($this->files->missing($filePath)) {
            $content = [];
        }

        if ($this->interfaceClass && $this->repositoryClass) {
            $content = array_merge($content, [
                $this->interfaceClass =>  $this->repositoryClass
            ]);
        }

        $stub = $this->files->get(__DIR__.'/../stubs/repositories.stub');
        $repo = '';
        foreach ($content as $key => $value) {
            $repo .= "\t\\" . $key . "::class => \\" . $value . "::class,\n";
        }
        $stub = str_replace('__RepositoryArray__', $repo, $stub);

        $this->files->put($filePath, $stub);
    }

    /**
     * Make repository directory
     *
     * @return void
     */
    public function makeRepositoryDirectory()
    {
        $repositoryPath = app_path().'/'.$this->defaultRepositoryNamespace;
        if ($this->files->exists($repositoryPath)) {
            return;
        }
        $this->files->makeDirectory($repositoryPath, 0777, true, true);
    }

    /**
     * Create base repository file.
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function createBaseRepository()
    {
        $name = $this->getDefaultRepositoryNamespace().'\\'.$this->baseRepository;
        $path = $this->getPath($name);

        if ($this->files->missing($path)) {
            $stub = __DIR__.'/../stubs/base-repository.stub';

            $this->files->put($path, $this->sortImports($this->buildRepository($name, $interface = null, $stub)));
        }

    }

    /**
     * Create repository file
     *
     * @param mixed $repo
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function createRepository($repo = null)
    {
        $repository = $repo ?? $this->repositoryInput;
        $name = $this->qualifyFile($repository);
        $path = $this->getPath($name);

        if ($this->files->missing($path)) {
            $this->makeDirectory($path);

            $this->files->put($path, $this->sortImports($this->buildRepository($name, $this->interfaceClass)));

            $this->repositoryClass = $name;
        }
    }

    /**
     * Build repository
     *
     * @param string $name
     * @param string $interface
     * @param string $baseStub
     *
     * @return void
     */
    protected function buildRepository($name, $interface, $baseStub = null)
    {
        $stub = $this->files->get($baseStub ?? $this->getRepositoryStub());

        return $this->replaceRepository($stub, $name, $interface);
    }

    /**
     * Replace repository content
     *
     * @param string $stub
     * @param string $name
     * @param string $interface
     *
     * @return void
     */
    public function replaceRepository(&$stub, $name, $interface)
    {
        $tmp = explode('\\', $interface);
        $interface = trim(array_pop($tmp));
        $model = str_replace('/', '\\', $this->modelClass);

        return str_replace(
            ['DummyNamespace', 'DummyRepositoryClass', 'DummyRepositoryInterface', 'DummyModel'],
            [$this->getNamespace($name), $this->replaceClassName($name), $interface, $model],
            $stub
        );
    }

    /**
     * Get repository stub file
     *
     * @return string
     */
    public function getRepositoryStub()
    {
        return __DIR__.'/../stubs/item-repository.stub';
    }

    /**
     * Create base interface file
     *
     * @return void
     */
    public function createBaseInterface()
    {
        $name = $this->getDefaultRepositoryNamespace().'\\'.$this->baseRepositoryInterface;
        $path = $this->getPath($name);

        if ($this->files->missing($path)) {
            $stub = __DIR__.'/../stubs/repository-interface.stub';

            $this->files->put($path, $this->sortImports($this->buildInterface($name, $stub)));
        }

    }

    /**
     * Create interface file
     *
     * @param mixed $interface
     *
     * @return void
     */
    public function createInterface($interface = null)
    {
        $interface = $interface ?? $this->interfaceInput;
        $interfaceName = $this->qualifyFile($interface);
        $path = $this->getPath($interfaceName);

        if ($this->files->missing($path)) {
            $this->makeDirectory($path);

            $this->files->put($path, $this->sortImports($this->buildInterface($interfaceName)));

            $this->interfaceClass = $interfaceName;
        }
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $interfaceName
     *
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildInterface($interfaceName, $baseStub = null)
    {
        $stub = $this->files->get($baseStub ?? $this->getInterfaceStub());

        return $this->replaceNamespaceInterface($stub, $interfaceName);
    }

    /**
     * Replace Namespace Interface
     *
     * @param  string $stub
     * @param  string $interfaceName
     *
     * @return string
     */
    public function replaceNamespaceInterface(&$stub, $interfaceName)
    {
        return str_replace(
            ['DummyNamespace', 'DummyItemRepositoryInterface'],
            [$this->getNamespace($interfaceName), $this->replaceClassName($interfaceName)],
            $stub
        );
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function replaceClassName($name)
    {
        return str_replace($this->getNamespace($name).'\\', '', $name);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getInterfaceStub()
    {
        return __DIR__.'/../stubs/item-repository-interface.stub';
    }

    /**
     * Get option inputs
     *
     * @return void
     */
    public function getOptionsInput()
    {
        $this->interfaceInput = $this->getInterfaceInput();
        $this->repositoryInput = $this->getRepositoryInput();
        $this->modelInput = $this->getModelInput();
    }

    /**
     * Get interface input
     *
     * @return void
     */
    public function getInterfaceInput()
    {
        return trim($this->option('interface'));
    }

    /**
     * Get repository input
     *
     * @return void
     */
    public function getRepositoryInput()
    {
        return trim($this->option('repository'));
    }

    /**
     * Get model input
     *
     * @return void
     */
    public function getModelInput()
    {
        return trim($this->option('model'));
    }

    /**
     * Check option input
     *
     * @return void
     *
     * @throws \Exception
     */
    public function checkOptionsInput()
    {
        if (empty($this->interfaceInput)) {
            throw new \Exception("Bad Request. Please input the Interface value");
        }

        if ($this->interfaceInput && empty($this->repositoryInput)) {
            throw new \Exception("Bad Request. Please input the Repository value");
        }

        if ($this->repositoryInput && empty($this->modelInput)) {
            throw new \Exception("Bad Request. Please input the Model value");
        }
    }

    /**
     * Get default repository namespace
     *
     * @return string
     */
    public function getDefaultRepositoryNamespace()
    {
        return $this->rootNamespace().''.$this->defaultRepositoryNamespace;
    }

    /**
     * Qualify file
     *
     * @param string $class
     *
     * @return string
     */
    public function qualifyFile($class)
    {
        $class = ltrim($class, '\\/');

        $rootNamespace = $this->getDefaultRepositoryNamespace();

        if (Str::startsWith($class, $rootNamespace)) {
            return $class;
        }

        $class = str_replace('/', '\\', $class);

        return $this->qualifyFile(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$class
        );
    }

        /**
     * Alphabetically sorts the imports for the given stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function sortImports($stub)
    {
        if (preg_match('/(?P<imports>(?:use [^;]+;$\n?)+)/m', $stub, $match)) {
            $imports = explode("\n", trim($match['imports']));

            if ($this->interfaceClass) {
                array_push($imports, "use $this->interfaceClass;");
            }

            sort($imports);

            return str_replace(trim($match['imports']), implode("\n", $imports), $stub);
        }

        return $stub;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return ;
    }
}
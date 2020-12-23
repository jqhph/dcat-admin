<?php

namespace Dcat\Admin\Console;

class ActionCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a admin action';

    /**
     * @var string
     */
    protected $choice;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var array
     */
    protected $namespaceMap = [
        'grid-batch' => 'Grid',
        'grid-row'   => 'Grid',
        'grid-tool'  => 'Grid',
        'form-tool'  => 'Form',
        'show-tool'  => 'Show',
        'tree-row'   => 'Tree',
        'tree-tool'  => 'Tree',
    ];

    public function handle()
    {
        $this->choice = $this->choice(
            'Which type of action would you like to make?',
            $choices = $this->actionTyps()
        );

        INPUT_NAME:

        $this->className = ucfirst(trim($this->ask('Please enter a name of action class')));

        if (! $this->className) {
            goto INPUT_NAME;
        }

        $this->namespace = ucfirst(trim($this->ask('Please enter the namespace of action class', $this->getDefaultNamespace(null))));

        $this->askBaseDirectory();

        return parent::handle();
    }

    /**
     * @return array
     */
    protected function actionTyps()
    {
        return [
            'default',
            'grid-batch',
            'grid-row',
            'grid-tool',
            'form-tool',
            'show-tool',
            'tree-row',
            'tree-tool',
        ];
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace(
            [
                'DummyName',
            ],
            [
                $this->className,
            ],
            $stub
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__."/stubs/actions/{$this->choice}.stub";
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->namespace) {
            return $this->namespace;
        }

        $segments = explode('\\', config('admin.route.namespace'));
        array_pop($segments);
        array_push($segments, 'Actions');

        if (isset($this->namespaceMap[$this->choice])) {
            array_push($segments, $this->namespaceMap[$this->choice]);
        }

        return implode('\\', $segments);
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        $this->type = $this->qualifyClass($this->className);

        return $this->className;
    }
}

<?php

namespace Dcat\Admin\Scaffold;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Support\Helper;

class ControllerCreator
{
    use GridCreator, FormCreator, ShowCreator;

    /**
     * Controller full name.
     *
     * @var string
     */
    protected $name;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * ControllerCreator constructor.
     *
     * @param  string  $name
     * @param  null  $files
     */
    public function __construct($name, $files = null)
    {
        $this->name = $name;

        $this->files = $files ?: app('files');
    }

    /**
     * Create a controller.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \Exception
     */
    public function create($model)
    {
        $path = $this->getPath($this->name);
        $dir = dirname($path);

        if (! is_dir($dir)) {
            $this->files->makeDirectory($dir, 0755, true);
        }

        if ($this->files->exists($path)) {
            throw new AdminException("Controller [$this->name] already exists!");
        }

        $stub = $this->files->get($this->getStub());

        $slug = str_replace('Controller', '', class_basename($this->name));

        $model = $model ?: 'App\Admin\Repositories\\'.$slug;

        $this->files->put($path, $this->replace($stub, $this->name, $model, $slug));
        $this->files->chmod($path, 0777);

        return $path;
    }

    /**
     * @param  string  $stub
     * @param  string  $name
     * @param  string  $model
     * @return string
     */
    protected function replace($stub, $name, $model, $slug)
    {
        $stub = $this->replaceClass($stub, $name);

        return str_replace(
            [
                'DummyModelNamespace',
                'DummyModel',
                'DummyTitle',
                '{controller}',
                '{grid}',
                '{form}',
                '{show}',
            ],
            [
                $model,
                class_basename($model),
                class_basename($model),
                $slug,
                $this->generateGrid(),
                $this->generateForm(),
                $this->generateShow(),
            ],
            $stub
        );
    }

    /**
     * Get controller namespace from giving name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(['DummyClass', 'DummyNamespace'], [$class, $this->getNamespace($name)], $stub);
    }

    /**
     * Get file path from giving controller name.
     *
     * @param  string  $name
     * @return string
     */
    public function getPath($name)
    {
        return Helper::guessClassFileName($name);
    }

    /**
     * Get stub file path.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/stubs/controller.stub';
    }
}

<?php

namespace Dcat\Admin\Scaffold;

class RepositoryCreator
{
    protected $path = 'Admin/Repositories';

    /**
     * @param string $controllerClass
     * @param string $modelClass
     *
     * @return string
     */
    public function create(string $controllerClass, string $modelClass)
    {
        $baseController = class_basename($controllerClass);
        $controller = str_replace('Controller', '', $baseController);

        $model = class_basename($modelClass);

        $files = app('files');

        $path = app_path("{$this->path}/{$controller}.php");
        $dir = dirname($path);

        if (! is_dir($dir)) {
            $files->makeDirectory($dir, 0755, true);
        }

        if (is_file($path)) {
            return;
        }

        $content = $files->get($this->stub());

        $files->put($path, str_replace([
            '{controllerClass}',
            '{baseController}',
            '{controller}',
            '{modelClass}',
            '{model}',
        ], [
            $controllerClass,
            $baseController,
            $controller,
            $modelClass,
            $model,
        ], $content));

        return $path;
    }

    protected function stub()
    {
        return __DIR__.'/stubs/repository.stub';
    }
}

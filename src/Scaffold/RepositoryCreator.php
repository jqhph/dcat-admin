<?php

namespace Dcat\Admin\Scaffold;

use Dcat\Admin\Support\Helper;

class RepositoryCreator
{
    /**
     * @param string $modelClass
     * @param string $repositoryClass
     *
     * @return string
     */
    public function create(?string $modelClass, ?string $repositoryClass)
    {
        $model = class_basename($modelClass);

        $files = app('files');

        $path = Helper::guessClassFileName($repositoryClass);
        $dir = dirname($path);

        if (! is_dir($dir)) {
            $files->makeDirectory($dir, 0755, true);
        }

        if (is_file($path)) {
            return;
        }

        $content = $files->get($this->stub());

        $files->put($path, str_replace([
            '{namespace}',
            '{class}',
            '{model}',
        ], [
            $this->getNamespace($repositoryClass),
            class_basename($repositoryClass),
            $modelClass,
        ], $content));

        $files->chmod($path, 0777);

        return $path;
    }

    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    protected function stub()
    {
        return __DIR__.'/stubs/repository.stub';
    }
}

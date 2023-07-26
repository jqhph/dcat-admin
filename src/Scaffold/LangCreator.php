<?php

namespace Dcat\Admin\Scaffold;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Facades\App;

class LangCreator
{
    protected $fields = [];

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * 生成语言包.
     *
     * @param  string  $controller
     * @param  string  $title
     * @return string
     */
    public function create(string $controller, ?string $title)
    {
        $controller = str_replace('Controller', '', class_basename($controller));

        $filename = $this->getLangPath($controller);
        if (is_file($filename)) {
            return;
        }

        $title = $title ?: $controller;

        $content = [
            'labels' => [
                $controller => $title,
                Helper::slug($controller) => $title,
            ],
            'fields'  => [],
            'options' => [],
        ];
        foreach ($this->fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            $content['fields'][$field['name']] = $field['translation'] ?: $field['name'];
        }

        $files = app('files');
        if ($files->put($filename, Helper::exportArrayPhp($content))) {
            $files->chmod($filename, 0777);

            return $filename;
        }
    }

    /**
     * 获取语言包路径.
     *
     * @param  string  $controller
     * @return string
     */
    protected function getLangPath(string $controller)
    {
        $path = rtrim(app()->langPath(), '/').'/'.App::getLocale();

        return $path.'/'.Helper::slug($controller).'.php';
    }
}

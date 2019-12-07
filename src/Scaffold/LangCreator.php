<?php

namespace Dcat\Admin\Scaffold;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

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
     * @param string $controller
     *
     * @return string
     */
    public function create(string $controller)
    {
        $controller = str_replace('Controller', '', class_basename($controller));

        $filename = $this->getLangPath($controller);
        if (is_file($filename)) {
            return;
        }

        $content = [
            'labels' => [
                $controller => $controller,
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

        if (app('files')->put($filename, Helper::exportArrayPhp($content))) {
            return $filename;
        }
    }

    /**
     * 获取语言包路径.
     *
     * @param string $controller
     *
     * @return string
     */
    protected function getLangPath(string $controller)
    {
        $path = resource_path('lang/'.App::getLocale());

        return $path.'/'.Str::slug($controller).'.php';
    }
}

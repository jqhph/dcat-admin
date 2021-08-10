<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

abstract class Editable extends AbstractDisplayer
{
    protected $type;

    protected $view;

    protected $options = [
        // 是否刷新页面
        'refresh' => false,
    ];

    public function display($options = [])
    {
        if (is_bool($options)) {
            $options = ['refresh' => $options];
        }

        $this->options = array_merge($this->options, $options);

        return admin_view($this->view, array_merge($this->variables(), $this->defaultOptions() + $this->options));
    }

    protected function defaultOptions()
    {
        return [];
    }

    public function variables()
    {
        return [
            'key'     => $this->getKey(),
            'class'   => $this->getSelector(),
            'type'    => $this->type,
            'display' => Helper::render($this->value),
            'value'   => $this->column->getOriginal(),
            'name'    => $this->column->getName(),
            'url'     => $this->getUrl(),
        ];
    }

    protected function getSelector()
    {
        return 'grid-editable-'.$this->type;
    }

    protected function getUrl()
    {
        return $this->resource().'/'.$this->getKey();
    }
}

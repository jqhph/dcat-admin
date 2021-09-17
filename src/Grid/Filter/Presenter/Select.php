<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Select extends Presenter
{
    /**
     * @var string
     */
    protected $elementClass = null;

    /**
     * Options of select.
     *
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $script;

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @var bool
     */
    protected $selectAll = true;

    /**
     * Select constructor.
     *
     * @param  mixed  $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * Set config for select2.
     *
     * all configurations see https://select2.org/configuration/options-api
     *
     * @param  string|array  $key
     * @param  mixed  $val
     * @return $this
     */
    public function config($key, $val = null)
    {
        if (is_array($key)) {
            $this->config = array_merge($this->config, $key);
        } else {
            $this->config[$key] = $val;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function disableSelectAll()
    {
        $this->selectAll = false;

        return $this;
    }

    /**
     * Build options.
     *
     * @return array
     */
    protected function buildOptions(): array
    {
        if (is_string($this->options)) {
            $this->loadRemoteOptions($this->options);
        }

        if ($this->options instanceof \Closure) {
            $this->options = $this->options->call($this->filter, $this->filter->getValue());
        }

        if ($this->options instanceof Arrayable) {
            $this->options = $this->options->toArray();
        }

        $this->addDefaultConfig([
            'allowClear'  => true,
            'placeholder' => [
                'id'   => '',
                'text' => $this->placeholder(),
            ],
        ]);

        return is_array($this->options) ? $this->options : [];
    }

    /**
     * Load options from current selected resource(s).
     *
     * @param  string  $model
     * @param  string  $idField
     * @param  string  $textField
     * @return $this
     */
    public function model($model, string $idField = 'id', string $textField = 'name')
    {
        if (! class_exists($model)
            || ! in_array(Model::class, class_parents($model))
        ) {
            throw new RuntimeException("[$model] must be a valid model class");
        }

        $this->options = function ($value) use ($model, $idField, $textField) {
            if (empty($value)) {
                return [];
            }

            $resources = [];

            if (is_array($value)) {
                if (Arr::isAssoc($value)) {
                    $resources[] = Arr::get($value, $idField);
                } else {
                    $resources = array_column($value, $idField);
                }
            } else {
                $resources[] = $value;
            }

            return $model::find($resources)->pluck($textField, $idField)->toArray();
        };

        return $this;
    }

    /**
     * Load options from remote.
     *
     * @param  string  $url
     * @param  array  $parameters
     * @param  array  $options
     * @return $this
     */
    protected function loadRemoteOptions(string $url, array $parameters = [], array $options = [])
    {
        $ajaxOptions = [
            'url' => Helper::urlWithQuery(admin_url($url), $parameters),
        ];
        $this->config([
            'allowClear'  => true,
            'placeholder' => [
                'id'   => '',
                'text' => $this->placeholder(),
            ],
        ]);

        $ajaxOptions = array_merge($ajaxOptions, $options);

        $values = array_filter((array) $this->filter->getValue());

        return $this->addVariables([
            'remote' => compact('ajaxOptions', 'values'),
        ]);
    }

    /**
     * @param  string|array  $key
     * @param  mixed  $value
     * @return $this
     */
    public function addDefaultConfig($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->addDefaultConfig($k, $v);
            }

            return $this;
        }

        if (! isset($this->config[$key])) {
            $this->config[$key] = $value;
        }

        return $this;
    }

    /**
     * Set input placeholder.
     *
     * @param  string  $placeholder
     * @return $this|string
     */
    public function placeholder(string $placeholder = null)
    {
        if ($placeholder === null) {
            return $this->placeholder ?: __('admin.choose');
        }

        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Load options from ajax.
     *
     * @param  string  $resourceUrl
     * @param $idField
     * @param $textField
     * @return $this
     */
    public function ajax(string $resourceUrl, string $idField = 'id', string $textField = 'text')
    {
        $this->config([
            'allowClear'         => true,
            'placeholder'        => $this->placeholder(),
            'minimumInputLength' => 1,
        ]);

        $url = admin_url($resourceUrl);

        return $this->addVariables(['ajax' => compact('url', 'idField', 'textField')]);
    }

    /**
     * @return array
     */
    public function defaultVariables(): array
    {
        return [
            'options'   => $this->buildOptions(),
            'class'     => $this->getElementClass(),
            'selector'  => $this->getElementClassSelector(),
            'selectAll' => $this->selectAll,
            'configs'   => $this->config,
        ];
    }

    public function getElementClassSelector()
    {
        return '.'.$this->getElementClass();
    }

    /**
     * @return string
     */
    public function getElementClass(): string
    {
        return $this->elementClass ?:
            ($this->elementClass = $this->getClass($this->filter->column()));
    }

    /**
     * Load options for other select when change.
     *
     * @param  string  $target
     * @param  string  $resourceUrl
     * @param  string  $idField
     * @param  string  $textField
     * @return $this
     */
    public function load($target, string $resourceUrl, string $idField = 'id', string $textField = 'text'): self
    {
        return $this->loads($target, $resourceUrl, $idField, $textField);
    }

    /**
     * 联动加载多个字段.
     *
     * @param  array|string  $fields
     * @param  array|string  $sourceUrls
     * @param  string  $idField
     * @param  string  $textField
     * @return $this
     */
    public function loads($fields = [], $sourceUrls = [], string $idField = 'id', string $textField = 'text')
    {
        $fieldsStr = implode('^', array_map(function ($field) {
            return $this->filter->formatColumnClass($field);
        }, (array) $fields));
        $urlsStr = implode('^', array_map(function ($url) {
            return admin_url($url);
        }, (array) $sourceUrls));

        return $this->addVariables(['loads' => [
            'fields'    => $fieldsStr,
            'urls'      => $urlsStr,
            'idField'   => $idField,
            'textField' => $textField,
            'group'     => 'form',
        ]]);
    }

    /**
     * Get form element class.
     *
     * @param  string  $target
     * @return mixed
     */
    protected function getClass($target): string
    {
        return str_replace('.', '_', $target);
    }
}

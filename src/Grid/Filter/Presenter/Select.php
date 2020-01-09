<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

use Dcat\Admin\Admin;
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
    protected $script = '';

    /**
     * @var bool
     */
    protected $selectAll = true;

    /**
     * Select constructor.
     *
     * @param mixed $options
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
     * @param string $key
     * @param mixed  $val
     *
     * @return $this
     */
    public function config($key, $val)
    {
        $this->config[$key] = $val;

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

        if (empty($this->script)) {
            $placeholder = json_encode([
                'id'   => '',
                'text' => trans('admin.choose'),
            ]);

            $configs = array_merge([
                'allowClear'         => true,
            ], $this->config);

            $configs = json_encode($configs);
            $configs = substr($configs, 1, strlen($configs) - 2);

            $this->script = <<<SCRIPT
$(".{$this->elementClass()}").select2({
  placeholder: $placeholder,
  $configs
});

SCRIPT;
        }

        Admin::script($this->script);

        return is_array($this->options) ? $this->options : [];
    }

    /**
     * Load options from current selected resource(s).
     *
     * @param string $model
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function model($model, $idField = 'id', $textField = 'name')
    {
        if (! class_exists($model)
            || ! in_array(Model::class, class_parents($model))
        ) {
            throw new \InvalidArgumentException("[$model] must be a valid model class");
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
     * @param string $url
     * @param array  $parameters
     * @param array  $options
     *
     * @return $this
     */
    protected function loadRemoteOptions($url, $parameters = [], $options = [])
    {
        $ajaxOptions = [
            'url' => $url.'?'.http_build_query($parameters),
        ];
        $configs = array_merge([
            'allowClear'  => true,
            'placeholder' => [
                'id'   => '',
                'text' => trans('admin.choose'),
            ],
        ], $this->config);

        $configs = json_encode($configs);
        $configs = substr($configs, 1, strlen($configs) - 2);

        $ajaxOptions = json_encode(array_merge($ajaxOptions, $options), JSON_UNESCAPED_UNICODE);

        $values = (array) $this->filter->getValue();
        $values = array_filter($values);
        $values = json_encode($values);

        $this->script = <<<JS

$.ajax($ajaxOptions).done(function(data) {
  $(".{$this->elementClass()}").select2({
    data: data,
    $configs
  }).val($values).trigger("change");
  
});

JS;
    }

    /**
     * Load options from ajax.
     *
     * @param string $resourceUrl
     * @param $idField
     * @param $textField
     */
    public function ajax($resourceUrl, $idField = 'id', $textField = 'text')
    {
        $configs = array_merge([
            'allowClear'         => true,
            'placeholder'        => trans('admin.choose'),
            'minimumInputLength' => 1,
        ], $this->config);

        $configs = json_encode($configs);
        $configs = substr($configs, 1, strlen($configs) - 2);

        $this->script = <<<JS

$(".{$this->elementClass()}").select2({
  ajax: {
    url: "$resourceUrl",
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term,
        page: params.page
      };
    },
    processResults: function (data, params) {
      params.page = params.page || 1;

      return {
        results: $.map(data.data, function (d) {
                   d.id = d.$idField;
                   d.text = d.$textField;
                   return d;
                }),
        pagination: {
          more: data.next_page_url
        }
      };
    },
    cache: true
  },
  $configs,
  escapeMarkup: function (markup) {
      return markup;
  }
});

JS;
    }

    /**
     * @return array
     */
    public function variables(): array
    {
        return [
            'options'   => $this->buildOptions(),
            'class'     => $this->elementClass(),
            'selectAll' => $this->selectAll,
        ];
    }

    /**
     * @return string
     */
    protected function elementClass(): string
    {
        return $this->elementClass ?:
            ($this->elementClass = $this->getClass($this->filter->column()));
    }

    /**
     * Load options for other select when change.
     *
     * @param string $target
     * @param string $resourceUrl
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function load($target, $resourceUrl, $idField = 'id', $textField = 'text'): self
    {
        $class = $this->elementClass();

        $script = <<<JS
$(document).off('change', ".{$class}");
$(document).on('change', ".{$class}", function () {
    var target = $(this).closest('form').find(".{$this->getClass($target)}");
    $.get("$resourceUrl?q="+this.value, function (data) {
        target.find("option").remove();
        $.each(data, function (i, item) {
            $(target).append($('<option>', {
                value: item.$idField,
                text : item.$textField
            }));
        });
        
        $(target).trigger('change');
    });
});
JS;

        Admin::script($script);

        return $this;
    }

    /**
     * Get form element class.
     *
     * @param string $target
     *
     * @return mixed
     */
    protected function getClass($target): string
    {
        return str_replace('.', '_', $target);
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('select2');
    }
}

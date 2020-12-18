<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Select extends Field
{
    use CanCascadeFields;

    public static $js = '@select2';
    public static $css = '@select2';

    protected $cascadeEvent = 'change';

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * Set options.
     *
     * @param array|\Closure|string $options
     *
     * @return $this|mixed
     */
    public function options($options = [])
    {
        if ($options instanceof \Closure) {
            $this->options = $options;

            return $this;
        }

        // remote options
        if (is_string($options)) {
            // reload selected
            if (class_exists($options) && in_array(Model::class, class_parents($options))) {
                return $this->model(...func_get_args());
            }

            return $this->loadRemoteOptions(...func_get_args());
        }

        $this->options = Helper::array($options);

        return $this;
    }

    /**
     * @param array $groups
     */

    /**
     * Set option groups.
     *
     * eg: $group = [
     *        [
     *        'label' => 'xxxx',
     *        'options' => [
     *            1 => 'foo',
     *            2 => 'bar',
     *            ...
     *        ],
     *        ...
     *     ]
     *
     * @param array $groups
     *
     * @return $this
     */
    public function groups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Load options for other select on change.
     *
     * @param string $field
     * @param string $sourceUrl
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function load($field, $sourceUrl, string $idField = 'id', string $textField = 'text')
    {
        if (Str::contains($field, '.')) {
            $field = $this->formatName($field);
        }

        $class = $this->normalizeElementClass($field);

        $sourceUrl = admin_url($sourceUrl);

        $script = <<<JS
$(document).off('change', "{$this->getElementClassSelector()}");
$(document).on('change', "{$this->getElementClassSelector()}", function () {
    var target = $(this).closest('.fields-group').find(".$class");
    
    if (String(this.value) !== '0' && ! this.value) {
        return;
    }
    $.ajax("$sourceUrl?q="+this.value).then(function (data) {
        target.find("option").remove();
        $(target).select2({
            data: $.map(data, function (d) {
                d.id = d.$idField;
                d.text = d.$textField;
                return d;
            })
        }).val(String(target.attr('data-value')).split(',')).trigger('change');
    });
});
$("{$this->getElementClassSelector()}").trigger('change');
JS;

        Admin::script($script);

        return $this;
    }

    /**
     * Load options for other selects on change.
     *
     * @param string $fields
     * @param string $sourceUrls
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function loads($fields = [], $sourceUrls = [], string $idField = 'id', string $textField = 'text')
    {
        $fieldsStr = implode('^', array_map(function ($field) {
            if (Str::contains($field, '.')) {
                return $this->normalizeElementClass($field).'_';
            }

            return $this->normalizeElementClass($field);
        }, (array) $fields));
        $urlsStr = implode('^', array_map(function ($url) {
            return admin_url($url);
        }, (array) $sourceUrls));

        $script = <<<JS
(function () {
    var fields = '$fieldsStr'.split('^');
    var urls = '$urlsStr'.split('^');
    
    var refreshOptions = function(url, target) {
        $.ajax(url).then(function(data) {
            target.find("option").remove();
            $(target).select2({
                data: $.map(data, function (d) {
                    d.id = d.$idField;
                    d.text = d.$textField;
                    return d;
                })
            }).val(String(target.data('value')).split(',')).trigger('change');
        });
    };
    
    $(document).off('change', "{$this->getElementClassSelector()}");
    $(document).on('change', "{$this->getElementClassSelector()}", function () {
        var _this = this;
        var promises = [];

        fields.forEach(function(field, index){
            var target = $(_this).closest('.fields-group').find('.' + fields[index]);

            if (_this.value !== '0' && ! _this.value) {
                return;
            }
            promises.push(refreshOptions(urls[index] + "?q="+ _this.value, target));
        });
    
        $.when(promises).then(function() {});
    });
    $("{$this->getElementClassSelector()}").trigger('change');
})()
JS;

        Admin::script($script);

        return $this;
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
    public function model($model, string $idField = 'id', string $textField = 'name')
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
    protected function loadRemoteOptions(string $url, array $parameters = [], array $options = [])
    {
        $ajaxOptions = [
            'url' => admin_url($url.'?'.http_build_query($parameters)),
        ];
        $configs = array_merge([
            'allowClear'  => true,
            'placeholder' => [
                'id'   => '',
                'text' => $this->placeholder(),
            ],
        ], $this->config);

        $configs = json_encode($configs);
        $configs = substr($configs, 1, strlen($configs) - 2);

        $ajaxOptions = json_encode(array_merge($ajaxOptions, $options));

        $this->script = <<<JS
$.ajax({$ajaxOptions}).done(function(data) {

  $("{$this->getElementClassSelector()}").each(function (_, select) {
      select = $(select);

      select.select2({
        data: data,
        $configs
      });
      
      var value = select.data('value') + '';
      
      if (value) {
        select.val(value.split(',')).trigger("change")
      }
  });
});

JS;

        return $this;
    }

    /**
     * Load options from ajax results.
     *
     * @param string $url
     * @param $idField
     * @param $textField
     *
     * @return $this
     */
    public function ajax(string $url, string $idField = 'id', string $textField = 'text')
    {
        $configs = array_merge([
            'allowClear'         => true,
            'placeholder'        => $this->placeholder(),
            'minimumInputLength' => 1,
        ], $this->config);

        $configs = json_encode($configs);
        $configs = substr($configs, 1, strlen($configs) - 2);

        $url = admin_url($url);

        $this->script = <<<JS

$("{$this->getElementClassSelector()}").select2({
  ajax: {
    url: "$url",
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

        return $this;
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
    public function config(string $key, $val)
    {
        $this->config[$key] = $val;

        return $this;
    }

    /**
     * Disable clear button.
     *
     * @return $this
     */
    public function disableClearButton()
    {
        return $this->config('allowClear', false);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        static::defineLang();

        $configs = array_merge([
            'allowClear'  => true,
            'placeholder' => [
                'id'   => '',
                'text' => $this->placeholder(),
            ],
        ], $this->config);

        $configs = json_encode($configs);

        if (empty($this->script)) {
            $this->script = "$(\"{$this->getElementClassSelector()}\").select2($configs);";
        }

        $this->addCascadeScript();

        if ($this->options instanceof \Closure) {
            $this->options = $this->options->bindTo($this->values());

            $this->options(call_user_func($this->options, $this->value(), $this));
        }

        $this->options = array_filter($this->options, 'strlen');

        $this->addVariables([
            'options' => $this->options,
            'groups'  => $this->groups,
        ]);

        $this->attribute('data-value', implode(',', Helper::array($this->value())));

        return parent::render();
    }

    /**
     * {@inheritdoc}
     */
    public function placeholder($placeholder = null)
    {
        if ($placeholder === null) {
            return $this->placeholder ?: $this->label;
        }

        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return void
     */
    public static function defineLang()
    {
        $lang = trans('select2');
        if (! is_array($lang) || empty($lang)) {
            return;
        }

        $locale = config('app.locale');

        Admin::script(
            <<<JS
(function () {
    if (! $.fn.select2) {
        return;
    }
    var e = $.fn.select2.amd;

    return e.define("select2/i18n/{$locale}", [], function () {
        return {
            errorLoading: function () {
                return "{$lang['error_loading']}"
            }, inputTooLong: function (e) {
                return "{$lang['input_too_long']}".replace(':num', e.input.length - e.maximum)
            }, inputTooShort: function (e) {
                return "{$lang['input_too_short']}".replace(':num', e.minimum - e.input.length)
            }, loadingMore: function () {
                return "{$lang['loading_more']}"
            }, maximumSelected: function (e) {
                return "{$lang['maximum_selected']}".replace(':num', e.maximum)
            }, noResults: function () {
                return "{$lang['no_results']}"
            }, searching: function () {
                 return "{$lang['searching']}"
            }
        }
    }), {define: e.define, require: e.require}
})()
JS
        );
    }
}

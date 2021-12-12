<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\JavaScript;

class Autocomplete extends Text
{
    use HasDepends;

    protected $view = 'admin::form.autocomplete';

    protected $groups = [];

    protected $groupBy = '__group__';

    protected $configs = [
        'autoSelectFirst' => true,
    ];

    public function __construct($column, $arguments = [])
    {
        $this->prepend('<i class="feather icon-edit-2"></i>');

        parent::__construct($column, $arguments);
    }

    public function datalist($entries = [])
    {
        return $this->options($entries);
    }

    /**
     * Set option groups.
     *
     * eg: $group = [
     *        [
     *        'label' => 'xxxx',
     *        'options' => [
     *            'foo',
     *            'bar',
     *            ...
     *        ],
     *        ...
     *     ]
     *
     * @param  array|\Closure  $groups
     * @return $this
     */
    public function groups($groups = [])
    {
        if ($groups instanceof \Closure) {
            $groups = $groups->call($this->data(), $this->value());
        }

        $this->groups = array_merge($this->groups, $groups);

        return $this;
    }

    /**
     * @param  array|\Closure  $options
     * @return $this|Autocomplete
     */
    public function options($options = [])
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this->data(), $this->value());
        }

        $this->options = array_merge($this->options, $this->formatOptions($options));

        return $this;
    }

    /**
     * Set config for autocomplete.
     *
     * all configurations see https://github.com/devbridge/jQuery-Autocomplete
     *
     * @param  array|\Closure  $configs
     * @return $this
     */
    public function configs($configs = [])
    {
        if ($configs instanceof \Closure) {
            $configs = $configs->call($this->data(), $this->value());
        }

        $this->configs = array_merge($this->configs, Helper::array($configs));

        return $this;
    }

    public function groupBy(string $groupBy)
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * Load options from ajax results.
     *
     * @param  string  $url
     * @param  string|null  $valueField
     * @param  string|null  $groupField
     * @return $this
     */
    public function ajax(string $url, string $valueField = '', string $groupField = '')
    {
        $url = admin_url($url);

        return $this->addVariables(['ajax' => compact('url', 'valueField', 'groupField')]);
    }

    public function render()
    {
        $this->formatGroupOptions();

        $this->configs([
            'groupBy' => $this->groupBy,
        ]);

        $this->addVariables([
            'options' => json_encode($this->options, \JSON_UNESCAPED_UNICODE),
            'configs' => JavaScript::format($this->configs),
        ]);

        return parent::render();
    }

    protected function formatGroupOptions()
    {
        foreach ($this->groups as $group) {
            if (! array_key_exists('options', $group) || ! array_key_exists('label', $group)) {
                continue;
            }

            $this->options = array_merge($this->options, $this->formatOptions($group['options'], $group['label']));
        }

        $this->groups = [];

        return $this;
    }

    protected function formatOptions($options, string $group = ''): array
    {
        return array_filter(array_map(function ($opt) use ($group) {
            if (! is_array($opt)) {
                $opt = ['value' => $opt, 'data' => []];
            }

            if (! array_key_exists('value', $opt)) {
                return null;
            }

            if ($group) {
                $opt['data'][$this->groupBy] = $group;
            }

            return $opt;
        }, Helper::array($options)));
    }
}

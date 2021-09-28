<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\JavaScript;

class Autocomplete extends Text
{
    protected $view = 'admin::form.autocomplete';

    protected $group = [];

    protected $configs = [
        'groupBy' => 'group',
    ];

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
     * @param  array  $groups
     * @return $this
     */
    public function groups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }

    public function options($options = [])
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this->data(), $this->value());
        }

        $options = array_map(function ($opt) {
            if (is_array($opt)) {
                if (! array_key_exists('value', $opt)) {
                    return null;
                }

                return $opt;
            }

            if (is_string($opt)) {
                return ['value' => $opt, 'data' => []];
            }

            return null;
        }, Helper::array($options));

        $this->options = array_merge($this->options, array_filter($options));

        return $this;
    }

    /**
     * Set config for autocomplete.
     *
     * all configurations see https://github.com/devbridge/jQuery-Autocomplete
     *
     * @param  array  $configs
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

    /**
     * Load options from ajax results.
     *
     * @param  string  $url
     * @param  string  $valueField
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

        $this->addVariables([
            'options' => json_encode($this->options),
            'configs' => JavaScript::format($this->configs),
        ]);

        return parent::render();
    }

    protected function formatGroupOptions()
    {
        foreach ($this->group as $group) {
            if (! array_key_exists('options', $group) || ! array_key_exists('label', $group)) {
                continue;
            }

            $this->options(array_map(function ($str) use ($group) {
                return ['value' => $str, 'data' => ['group' => $group['label']]];
            }, $group['options']));
        }

        $this->group = [];

        return $this;
    }
}

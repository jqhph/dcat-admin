<?php

namespace Dcat\Admin\Widgets\Chart;

/**
 * @see https://www.chartjs.org/docs/latest/charts/doughnut.html
 */
class Pie extends Chart
{
    protected $type = 'pie';

    /**
     * Add datasets.
     *
     * @example
     *     $this->add([1, 23, 6, 10, 6]);
     *
     * @param string|array $label
     * @param array        $data
     * @param string|array $fillColor
     *
     * @return $this
     */
    public function add($data = [], $fillColor = null, $none = null)
    {
        $item = [
            'data' => $data,
        ];

        if ($fillColor) {
            if (is_string($fillColor)) {
                $item['backgroundColor'] = $fillColor;
            } elseif (is_array($fillColor)) {
                $item = array_merge($fillColor, $item);
            }
        }

        $this->data['datasets'][] = $item;

        return $this;
    }

    /**
     * Fill default color.
     *
     * @param array $colors
     */
    protected function fillColor(array $colors = [])
    {
        foreach ($this->data['datasets'] as &$item) {
            $colors = $colors ?: $this->colors;
            if (empty($item['backgroundColor'])) {
                $item['backgroundColor'] = $colors;
            }
        }
    }

    public function cutoutPercentage($percent)
    {
        return $this->options(['cutoutPercentage' => $percent]);
    }

    public function circumference($val)
    {
        return $this->options(['circumference' => $val]);
    }

    public function animateScale(bool $val = true)
    {
        return $this->animation(['animateScale' => $val]);
    }
}

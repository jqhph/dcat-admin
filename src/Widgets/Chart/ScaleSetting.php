<?php

namespace Dcat\Admin\Widgets\Chart;

trait ScaleSetting
{
    /**
     * @see https://www.chartjs.org/docs/latest/axes/
     *
     * @param array $opts
     *
     * @return $this
     */
    public function scales(array $opts)
    {
        if (! isset($this->options['scales'])) {
            $this->options['scales'] = [];
        }

        $this->options['scales'] = array_merge($this->options['scales'], $opts);

        return $this;
    }

    /**
     * @param string|null $label
     *
     * @return $this
     */
    public function displayScaleLabelOnX(?string $label)
    {
        return $this->xAxes([
            [
                'scaleLabel' => [
                    'display'     => true,
                    'labelString' => $label,
                ],
            ],
        ]);
    }

    /**
     * @param string|null $label
     *
     * @return $this
     */
    public function displayScaleLabelOnY(?string $label)
    {
        return $this->yAxes([
            [
                'scaleLabel' => [
                    'display'     => true,
                    'labelString' => $label,
                ],
            ],
        ]);
    }

    /**
     * @param array $opts
     *
     * @return $this
     */
    public function yAxes(array $opts)
    {
        return $this->scales(['yAxes' => $opts]);
    }

    /**
     * @param array $opts
     *
     * @return $this
     */
    public function xAxes(array $opts)
    {
        return $this->scales(['xAxes' => $opts]);
    }

    /**
     * @see https://www.chartjs.org/docs/latest/axes/radial/linear.html
     *
     * @param array $opts
     *
     * @return $this
     */
    public function scale(array $opts)
    {
        if (! isset($this->options['scale'])) {
            $this->options['scale'] = [];
        }

        $this->options['scale'] = array_merge($this->options['scale'], $opts);

        return $this;
    }
}

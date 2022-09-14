<?php

namespace Dcat\Admin\Grid\Displayers;

use Carbon\CarbonInterface;

/**
 * Carbon 日期格式化.
 *
 * @example $grid->column('created_at')->carbon();
 *          $grid->column('created_at')->carbon('Y-m-d H:i:s', '-');
 *          $grid->column('created_at')->carbon(fn ($value) => $value->format('Y-m-d'));
 */
class Carbon extends AbstractDisplayer
{
    /**
     * @param  string|\Closure  $format
     * @param  mixed  $default
     * @return string|null
     */
    public function display(string|\Closure $format = 'Y-m-d H:i:s', mixed $default = ''): ?string
    {
        if (! $this->value instanceof CarbonInterface) {
            return $default;
        }

        if ($format instanceof \Closure) {
            return $format($this->value);
        }

        return $this->value?->format($format) ?? $default;
    }
}

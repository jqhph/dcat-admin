<?php

namespace Dcat\Admin\Widgets\Sparkline;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @see https://omnipotent.net/jquery.sparkline
 *
 * @method $this barColor(string $val)
 * @method $this negBarColor(string $val)
 * @method $this zeroColor(string $val)
 * @method $this nullColor(string $val)
 * @method $this barWidth(int $val)
 * @method $this zeroAxis(bool $val)
 * @method $this colorMap(array $val)
 */
class Bar extends Sparkline
{
    protected $type = 'bar';
}

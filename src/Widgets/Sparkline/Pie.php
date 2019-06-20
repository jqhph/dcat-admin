<?php

namespace Dcat\Admin\Widgets\Sparkline;

/**
 * @see https://omnipotent.net/jquery.sparkline
 *
 * @method $this borderWidth($val)
 * @method $this borderColor(string $val)
 * @method $this sliceColors(array $val)
 * @method $this offset(int $val)
 */
class Pie extends Sparkline
{
    protected $type = 'pie';
}

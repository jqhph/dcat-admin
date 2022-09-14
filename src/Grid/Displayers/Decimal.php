<?php

namespace Dcat\Admin\Grid\Displayers;

/**
 * number format.
 *
 * @example $grid->column('price')->decimal();
 *          $grid->column('price')->decimal(2);
 */
class Decimal extends AbstractDisplayer
{
    /**
     * @param  int  $length
     *
     * @return string
     */
    public function display(int $length = 2): string
    {
        return sprintf('%.'.$length.'f', $this->value);
    }
}

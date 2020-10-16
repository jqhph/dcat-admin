<?php

namespace Dcat\Admin\Grid\Events;

use Dcat\Admin\Grid;

abstract class Event
{
    /**
     * @var Grid
     */
    public $grid;

    public $payload = [];

    public function __construct(Grid $grid, array $payload = [])
    {
        $this->grid = $grid;
        $this->payload = $payload;
    }
}

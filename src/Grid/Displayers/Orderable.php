<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;

class Orderable extends AbstractDisplayer
{
    public function __construct($value, Grid $grid, Column $column, $row)
    {
        parent::__construct($value, $grid, $column, $row);

        if (! trait_exists('\Spatie\EloquentSortable\SortableTrait')) {
            throw new \Exception('To use orderable grid, please install package [spatie/eloquent-sortable] first.');
        }
    }

    public function display()
    {
        Admin::script($this->script());

        return <<<EOT

<div class="">
    <a href="javascript:void(0)" class=" font-14 {$this->grid->rowName()}-orderable" data-id="{$this->key()}" data-direction="1">
        <i class="fa fa-hand-o-up fa-fw"></i>
    </a>
    <a href="javascript:void(0)" class=" font-14 {$this->grid->rowName()}-orderable" data-id="{$this->key()}" data-direction="0">
        <i class="fa fa-hand-o-down fa-fw"></i>
    </a>
</div>
EOT;
    }

    protected function script()
    {
        return <<<JS

$('.{$this->grid->rowName()}-orderable').on('click', function() {

    var key = $(this).data('id');
    var direction = $(this).data('direction');

    $.post('{$this->resource()}/' + key, {_method:'PUT', _token:LA.token, _orderable:direction}, function(data){
        if (data.status) {
            LA.reload();
            LA.success(data.message);
        }
    });
});
JS;
    }
}

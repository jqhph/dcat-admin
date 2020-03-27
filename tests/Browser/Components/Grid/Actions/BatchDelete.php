<?php

namespace Tests\Browser\Components\Grid\Actions;

class BatchDelete extends Delete
{
    /**
     * 读取组件的元素快捷方式.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@item' => 'a[data-action="batch-delete"]:visible',
            '@confirm' => '.swal2-confirm',
            '@cancel' => '.swal2-cancel',
        ];
    }
}

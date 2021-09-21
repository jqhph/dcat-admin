<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Actions\Action;

class TextActions extends Action
{
    /**
     * @return string
     */
    protected function getViewLabel()
    {
        $label = trans('admin.show');

        return "{$label} &nbsp;";
    }


    /**
     * @return string
     */
    protected function getEditLabel()
    {
        $label = trans('admin.edit');

        return "{$label} &nbsp;";
    }

    /**
     * @return string
     */
    protected function getQuickEditLabel()
    {
        $label = trans('admin.quick_edit');

        return "{$label} &nbsp;";
    }

    /**
     * @return string
     */
    protected function getDeleteLabel()
    {
        $label = trans('admin.delete');

        return "{$label} &nbsp;";
    }
}

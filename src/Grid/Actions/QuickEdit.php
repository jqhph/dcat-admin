<?php

namespace Dcat\Admin\Grid\Actions;

use Dcat\Admin\Form;
use Dcat\Admin\Grid\RowAction;

class QuickEdit extends RowAction
{
    protected static $resolvedWindow;

    /**
     * @return array|null|string
     */
    public function title()
    {
        return '<i class="feather icon-edit"></i> '.__('admin.quick_edit');
    }

    public function render()
    {
        if (! static::$resolvedWindow) {
            static::$resolvedWindow = true;

            [$width, $height] = $this->parent->option('dialog_form_area');

            $title = trans('admin.edit');

            Form::dialog($title)
                ->click(".{$this->getElementClass()}")
                ->dimensions($width, $height)
                ->forceRefresh()
                ->success('Dcat.reload()');
        }

        $this->setHtmlAttribute([
            'data-url' => "{$this->resource()}/{$this->getKey()}/edit",
        ]);

        return parent::render();
    }

    public function makeSelector()
    {
        return 'quick-edit';
    }
}

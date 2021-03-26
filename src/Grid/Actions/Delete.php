<?php

namespace Dcat\Admin\Grid\Actions;

use Dcat\Admin\Grid\RowAction;

class Delete extends RowAction
{
    /**
     * @return array|null|string
     */
    public function title()
    {
        if ($this->title) {
            return $this->title;
        }

        return '<i class="feather icon-trash"></i> '.__('admin.delete').' &nbsp;&nbsp;';
    }

    public function render()
    {
        $this->setHtmlAttribute([
            'data-url'      => $this->url(),
            'data-message'  => "ID - {$this->getKey()}",
            'data-action'   => 'delete',
            'data-redirect' => $this->redirectUrl(),
        ]);

        return parent::render();
    }

    protected function redirectUrl()
    {
        return $this->parent->model()->withoutTreeQuery(request()->fullUrl());
    }

    public function url()
    {
        return "{$this->resource()}/{$this->getKey()}";
    }
}

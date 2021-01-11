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
        return '<i class="feather icon-trash"></i> '.__('admin.delete');
    }

    public function render()
    {
        $this->setHtmlAttribute([
            'data-url'     => $this->url(),
            'data-message' => "ID - {$this->getKey()}",
            'data-action'  => 'delete',
        ]);

        return parent::render();
    }

    public function url()
    {
        return "{$this->resource()}/{$this->getKey()}";
    }
}

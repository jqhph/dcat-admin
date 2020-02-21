<?php

namespace Dcat\Admin;

use Dcat\Admin\Layout\Content;

class SimpleGrid extends Grid
{
    const QUERY_NAME = '_mini';

    public function __construct($repository, $builder = null)
    {
        parent::__construct($repository, $builder);

        $this->setName('simple');
        $this->disableCreateButton();
        $this->disableActions();
        $this->disableExporter();
        $this->disablePerPages();
        $this->disableBatchActions();

        $this->option('row_selector_clicktr', true);

        $this->tools->disableBatchActions();
        $this->tools->disableFilterButton();

        Content::composing(function (Content $content) {
            $content->simple();
        }, true);
    }

    protected function setupFilter()
    {
        parent::setupFilter();

        $this->filter->panel();
    }
}

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
        $this->disablePerPages();
        $this->disableBatchActions();
        $this->disableFilterButton();

        $this->rowSelector()->click();

        Content::composing(function (Content $content) {
            Admin::style('#app{padding: 1rem}');

            $content->full();
        }, true);
    }
}

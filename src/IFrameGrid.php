<?php

namespace Dcat\Admin;

use Dcat\Admin\Layout\Content;

/**
 * @deprecated 即将在2.0版本中废弃
 */
class IFrameGrid extends Grid
{
    const QUERY_NAME = '_grid_iframe_';

    public function __construct($repository, $builder = null)
    {
        parent::__construct($repository, $builder);

        $this->setName('simple');
        $this->disableCreateButton();
        $this->disableActions();
        $this->disablePerPages();
        $this->disableBatchActions();

        $this->rowSelector()->click();

        Content::composing(function (Content $content) {
            Admin::style('#app{padding: 1.4rem 1rem 1rem}');

            $content->full();
        }, true);
    }
}

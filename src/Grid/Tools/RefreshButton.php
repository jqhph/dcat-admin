<?php

namespace Dcat\Admin\Grid\Tools;

use Illuminate\Contracts\Support\Renderable;

class RefreshButton implements Renderable
{
    /**
     * Render refresh button of grid.
     *
     * @return string
     */
    public function render()
    {
        $refresh = trans('admin.refresh');

        return <<<EOT
<a data-action="refresh" class="btn btn-sm btn-primary grid-refresh btn-mini" style="margin-right:3px"><i class="fa fa-refresh"></i><span class="hidden-xs">&nbsp; $refresh</span></a>
EOT;
    }
}

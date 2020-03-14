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
<button data-action="refresh" class="btn btn-outline-primary grid-refresh btn-mini" style="margin-right:3px">
    <i class="feather icon-refresh-cw"></i><span class="hidden-xs">&nbsp; $refresh</span>
</button>
EOT;
    }
}

<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Concerns\QuickSearch as QuickSearchConcerns;
use Illuminate\Support\Arr;

class QuickSearch extends AbstractTool
{
    /**
     * @var string
     */
    protected $view = 'admin::grid.quick-search';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $query = request()->query();

        Arr::forget($query, QuickSearchConcerns::$searchKey);

        $vars = [
            'action' => request()->url() . '?' . http_build_query($query),
            'key' => QuickSearchConcerns::$searchKey,
            'value' => request(QuickSearchConcerns::$searchKey),
        ];

        return view($this->view, $vars);
    }
}

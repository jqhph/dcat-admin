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
     * @var string
     */
    protected $placeholder = null;

    /**
     * Set placeholder.
     *
     * @param string $text
     *
     * @return $this
     */
    public function placeholder(?string $text = '')
    {
        $this->placeholder = $text;

        return $this;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $request = request();
        $query = $request->query();

        Arr::forget($query, [
            QuickSearchConcerns::$searchKey,
            $this->grid->model()->getPageName(),
            '_pjax',
        ]);

        $vars = [
            'action' => $request->url() . '?' . http_build_query($query),
            'key' => QuickSearchConcerns::$searchKey,
            'value' => request(QuickSearchConcerns::$searchKey),
            'placeholder' => $this->placeholder ?: trans('admin.search'),
        ];

        return view($this->view, $vars);
    }
}

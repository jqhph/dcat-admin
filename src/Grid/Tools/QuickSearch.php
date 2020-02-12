<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
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
     * @var string
     */
    protected $queryName = '__search__';

    /**
     * @var int rem
     */
    protected $width = 25;

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setQueryName(?string $name)
    {
        $this->queryName = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function queryName()
    {
        return $this->queryName;
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function width(int $width)
    {
        $this->width = $width;

        return $this;
    }

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
     * @return string
     */
    public function value()
    {
        return request($this->queryName);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $this->setupScript();

        $request = request();
        $query = $request->query();

        Arr::forget($query, [
            $this->queryName,
            $this->parent->model()->getPageName(),
            '_pjax',
        ]);

        $vars = [
            'action'      => $request->url().'?'.http_build_query($query),
            'key'         => $this->queryName,
            'value'       => $this->value(),
            'placeholder' => $this->placeholder ?: trans('admin.search'),
            'width'       => $this->width,
        ];

        return view($this->view, $vars);
    }

    protected function setupScript()
    {
        $script = <<<'JS'
var show = function () {
    var t = $(this),
        clear = t.parent().find('.quick-search-clear');

    if (t.val()) {
        clear.css({color: '#333'});
    } else {
        clear.css({color: '#fff'});
    }
    return false;
};

$('input.quick-search-input').on('focus', show).on('keyup', show);

$('.quick-search-clear').click(function () {
    $(this).parent().find('.quick-search-input').val('');

    $(this).closest('form').submit();
});
JS;

        Admin::script($script);
    }
}

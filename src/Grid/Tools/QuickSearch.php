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
    protected $width = 29;

    public function __construct($key = null, $title = null)
    {
        parent::__construct($key, $title);
    }

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
        return trim(request($this->queryName));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $request = request();
        $query = $request->query();

        $this->setupScript();

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
(function () {
    var inputting = false,
        $ipt = $('input.quick-search-input'), 
        val = $ipt.val(),
        ignoreKeys = [16, 17, 18, 20, 35, 36, 37, 38, 39, 40, 45, 144];
    
    var submit = LA.debounce(function (input) {
        inputting || $(input).parents('form').submit()
    }, 600);
    
    function toggleBtn() {
        var t = $(this),
            btn = t.parent().find('.quick-search-clear');
    
        if (t.val()) {
            btn.css({color: '#333'});
        } else {
            btn.css({color: '#fff'});
        }
        return false;
    }
    
    $ipt.on('focus', toggleBtn)
        .on('keyup', function (e) {
            toggleBtn.apply(this);
            
            ignoreKeys.indexOf(e.keyCode) == -1 && submit(this)
        })
        .on('mousemove', toggleBtn)
        .on('mouseout', toggleBtn)
        .on('compositionstart', function(){
            inputting = true
        })
        .on('compositionend', function() {
            inputting = false
        });
    val !== '' && $ipt.val('').focus().val(val);
    
    $('.quick-search-clear').click(function () {
        $(this).parent().find('.quick-search-input').val('');
    
        $(this).closest('form').submit();
    });
})()
JS;

        Admin::script($script);
    }
}

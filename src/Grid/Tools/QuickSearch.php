<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

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
    protected $queryName = '_search_';

    /**
     * @var int rem
     */
    protected $width = 19;

    /**
     * @var bool
     */
    protected $autoSubmit = true;

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
    public function getQueryName()
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
     * @return string
     */
    public function formAction()
    {
        return Helper::fullUrlWithoutQuery([
            $this->queryName,
            $this->parent->model()->getPageName(),
            '_pjax',
        ]);
    }

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function auto(bool $value = true)
    {
        $this->autoSubmit = $value;

        return $this;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $this->setupScript();

        $data = [
            'action'      => $this->formAction(),
            'key'         => $this->queryName,
            'value'       => $this->value(),
            'placeholder' => $this->placeholder ?: trans('admin.search'),
            'width'       => $this->width,
            'auto'        => $this->autoSubmit,
        ];

        return view($this->view, $data);
    }

    protected function setupScript()
    {
        $script = <<<'JS'
(function () {
    var inputting = false,
        $ipt = $('input.quick-search-input'), 
        val = $ipt.val(),
        ignoreKeys = [16, 17, 18, 20, 35, 36, 37, 38, 39, 40, 45, 144],
        auto = $ipt.attr('auto');
    
    var submit = Dcat.helpers.debounce(function (input) {
        inputting || $(input).parents('form').submit()
    }, 1200);
    
    function toggleBtn() {
        var t = $(this),
            btn = t.parent().parent().find('.quick-search-clear');
    
        if (t.val()) {
            btn.css({color: '#333', cursor: 'pointer'});
        } else {
            btn.css({color: '#fff', cursor: 'none'});
        }
        return false;
    }
    
    $ipt.on('focus', toggleBtn)
        .on('mousemove', toggleBtn)
        .on('mouseout', toggleBtn)
        .on('compositionstart', function(){
            inputting = true
        })
        .on('compositionend', function() {
            inputting = false
        });

    if (auto > 0) {
        $ipt.on('keyup', function (e) {
            toggleBtn.apply(this);
            
            ignoreKeys.indexOf(e.keyCode) == -1 && submit(this)
        })
    }
    
    val !== '' && $ipt.val('').focus().val(val);
    
    $('.quick-search-clear').on('click', function () {
        $(this).parent().find('.quick-search-input').val('');
    
        $(this).closest('form').submit();
    });
})()
JS;

        Admin::script($script);
    }
}

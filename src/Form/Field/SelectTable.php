<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\TableModal;
use Dcat\Admin\Grid\LazyRenderable;

class SelectTable extends Field
{
    use PlainInput;

    /**
     * @var TableModal
     */
    protected $modal;

    protected $style = 'primary';

    public function __construct($column, $arguments = [])
    {
        parent::__construct($column, $arguments);

        $this->modal = TableModal::title($this->label);
    }

    /**
     * 设置弹窗标题.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->modal->title($title);

        return $this;
    }

    /**
     * 设置尺寸为 xl.
     *
     * @return $this
     */
    public function xl()
    {
        $this->modal->xl();

        return $this;
    }

    /**
     * 设置表格异步渲染实例.
     *
     * @param LazyRenderable $renderable
     *
     * @return $this
     */
    public function from(LazyRenderable $renderable)
    {
        $this->modal->body($renderable);

        return $this;
    }

    /**
     * 转化为数组格式保存.
     *
     * @param mixed $value
     *
     * @return array|mixed
     */
    public function prepareInputValue($value)
    {
        return Helper::array($value, true);
    }

    protected function formatOptions()
    {
        $value = Helper::array(old($this->column, $this->value()));

        if ($this->options instanceof \Closure) {
            $this->options = $this->options->call($this->values(), $value, $this);
        }

        $values = [];

        foreach (Helper::array($this->options) as $id => $label) {
            foreach ($value as $v) {
                if ($v == $id && $v !== null) {
                    $values[] = ['id' => $v, 'label' => $label];
                }
            }
        }

        $this->options = json_encode($values);
    }

    protected function addScript()
    {
        $this->script .= <<<JS
(function () {
    var modal = $(replaceNestedFormIndex('#{$this->modal->getId()}'));
    var input = $(replaceNestedFormIndex('#hidden-{$this->id}'));
    var options = {$this->options};
    
    function getSelectedRows() {
        var selected = [], ids = [];
    
        modal.find('.checkbox-grid-column input[type="checkbox"]:checked').each(function() {
            var id = $(this).data('id'), i, exist;
    
            for (i in selected) {
                if (selected[i].id === id) {
                    exist = true
                }
            }
    
            if (! exist) {
                selected.push({'id': id, 'label': $(this).data('label')});
                ids.push(id)
            }
        });
    
        return [selected, ids];
    }
    
    function setKeys(ids) {
        input.val(ids.length ? ids.join(',') : '');
    }
            
    $(replaceNestedFormIndex('#{$this->getButtonId()}')).on('click', function () {
        var selected = getSelectedRows();
        
        setKeys(selected[1]);
        
        render(selected[0]);
        
        $(this).parents('.modal').modal('toggle');
    });
    
    function render(selected) {
        var box = $('{$this->getElementClassSelector()}'),
            placeholder = box.find('.default-text'),
            option = box.find('.option');
        
        if (! selected) {
            placeholder.removeClass('d-none');
            option.addClass('d-none');
            
            return;
        }
        
        placeholder.addClass('d-none');
        option.removeClass('d-none');
        
        var remove = $("<div class='pull-right ' style='font-weight:bold;cursor:pointer'>×</div>");

        option.text(selected[0]['label']);
        option.append(remove);
        
        remove.on('click', function () {
            setKeys([]);
            placeholder.removeClass('d-none');
            option.addClass('d-none');
        });
    }
    
    render(options[0]);
})();
JS;
    }

    protected function setUpModal()
    {
        $this->modal
            ->join()
            ->id($this->getElementId())
            ->runScript(false)
            ->footer($this->renderFooter())
            ->onLoad($this->getOnLoadScript());
    }

    protected function getOnLoadScript()
    {
        // 实现单选效果
        return <<<JS
$(this).find('.checkbox-grid-header').remove();

var checkbox = $(this).find('.checkbox-grid-column input[type="checkbox"]');

checkbox.on('change', function () {
    var id = $(this).data('id');
    
    checkbox.each(function () {
        if ($(this).data('id') != id) {
            $(this).prop('checked', false);
            $(this).parents('tr').css('background-color', '');
        }
    });
});
JS;
    }

    public function render()
    {
        $this->setUpModal();
        $this->formatOptions();

        $name = $this->getElementName();

        $this->prepend('<i class="feather icon-arrow-up"></i>')
            ->defaultAttribute('class', 'form-control '. $this->getElementClassString())
            ->defaultAttribute('type', 'text')
            ->defaultAttribute('name', $name);

        $this->addVariables([
            'prepend'     => $this->prepend,
            'append'      => $this->append,
            'style'       => $this->style,
            'modal'       => $this->modal->render(),
            'placeholder' => $this->placeholder(),
        ]);

        $this->script = $this->modal->getScript();

        $this->addScript();
        //dd($this->script);
        return parent::render();
    }

    /**
     * 弹窗底部内容构建.
     *
     * @return string
     */
    protected function renderFooter()
    {
        $submit = trans('admin.submit');
        $cancel = trans('admin.cancel');

        return <<<HTML
<a id="{$this->getButtonId()}" class="btn btn-primary" style="color: #fff">&nbsp;{$submit}&nbsp;</a>&nbsp;
<a onclick="$(this).parents('.modal').modal('toggle')" class="btn btn-white">&nbsp;{$cancel}&nbsp;</a>
HTML;
    }

    /**
     * 提交按钮ID
     *
     * @return string
     */
    protected function getButtonId()
    {
        return 'submit-'.$this->id;
    }
}

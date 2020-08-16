<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\TableModal;

class SelectTable extends Field
{
    use PlainInput;

    protected static $js = [
        '@select-table',
    ];

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
Dcat.grid.SelectTable({
    modal: replaceNestedFormIndex('#{$this->modal->id()}'),
    container: replaceNestedFormIndex('#{$this->getAttribute('id')}'),
    input: replaceNestedFormIndex('#hidden-{$this->id}'),
    button: replaceNestedFormIndex('#{$this->getButtonId()}'),
    values: {$this->options},
});
JS;
    }

    protected function setUpModal()
    {
        $table = $this->modal->getTable();

        $table->id('table-card-'.$this->getElementId());

        $this->modal
            ->join()
            ->id($this->getElementId())
            ->runScript(false)
            ->footer($this->renderFooter());

        // 显示弹窗的时候异步加载表格
        $this->modal->getModal()->onShow(
            <<<JS
if (! modal.table) {
    modal.table = $(replaceNestedFormIndex('{$table->getElementSelector()}'));
}            
modal.table.trigger('table:load');
JS
        );
    }

    public function render()
    {
        $this->setUpModal();
        $this->formatOptions();

        $name = $this->getElementName();

        $this->prepend('<i class="feather icon-arrow-up"></i>')
            ->defaultAttribute('class', 'form-control '.$this->getElementClassString())
            ->defaultAttribute('type', 'text')
            ->defaultAttribute('name', $name)
            ->defaultAttribute('id', 'container-'.$this->getElementId());

        $this->addVariables([
            'prepend'     => $this->prepend,
            'append'      => $this->append,
            'style'       => $this->style,
            'modal'       => $this->modal->render(),
            'placeholder' => $this->placeholder(),
        ]);

        $this->script = $this->modal->getScript();

        $this->addScript();
//        dd($this->script);
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
     * 提交按钮ID.
     *
     * @return string
     */
    public function getButtonId()
    {
        return 'submit-'.$this->id;
    }
}

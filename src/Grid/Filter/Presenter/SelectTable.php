<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\TableModal;
use Illuminate\Support\Str;

class SelectTable extends Presenter
{
    public static $js = [
        '@select-table',
    ];

    /**
     * @var TableModal
     */
    protected $modal;

    protected $title;

    protected $id;

    protected $options;

    protected $placeholder;

    public function __construct(LazyRenderable $table)
    {
        $this->modal = TableModal::make($table);
        $this->id = 'select-table-filter-'.Str::random(8);
    }

    /**
     * 设置选中的选项.
     *
     * @param \Closure $options
     *
     * @return $this
     */
    public function options(\Closure $options)
    {
        $this->options = $options;

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
     * 设置弹窗标题.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $placeholder
     *
     * @return $this|string
     */
    public function placeholder(string $placeholder = null)
    {
        if ($placeholder === null) {
            return $this->placeholder ?: __('admin.choose');
        }

        $this->placeholder = $placeholder;

        return $this;
    }

    protected function setUpModal()
    {
        $table = $this->modal->getTable();

        $table->id('table-card-'.$this->id);

        $this->modal
            ->id('modal-'.$this->id)
            ->title($this->title ?: $this->filter->getLabel())
            ->footer($this->renderFooter());
    }

    protected function formatOptions()
    {
        $value = Helper::array($this->value());

        if ($this->options instanceof \Closure) {
            $this->options = call_user_func($this->options, $value, $this);
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
        Admin::script(
            <<<JS
Dcat.grid.SelectTable({
    modal: replaceNestedFormIndex('#{$this->modal->id()}'),
    container: replaceNestedFormIndex('#{$this->id}'),
    input: replaceNestedFormIndex('#hidden-{$this->id}'),
    button: replaceNestedFormIndex('#{$this->getButtonId()}'),
    values: {$this->options},
});
JS
        );
    }

    /**
     * @return array
     */
    public function variables(): array
    {
        $this->formatOptions();
        $this->setUpModal();
        $this->addScript();

        return [
            'id'          => $this->id,
            'modal'       => $this->modal,
            'placeholder' => $this->placeholder(),
        ];
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

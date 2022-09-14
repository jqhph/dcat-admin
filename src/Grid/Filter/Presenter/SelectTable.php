<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\DialogTable;
use Illuminate\Support\Str;

class SelectTable extends Presenter
{
    public static $js = [
        '@select-table',
    ];

    /**
     * @var DialogTable
     */
    protected $dialog;

    protected $style = 'primary';

    protected $id;

    protected $options;

    protected $placeholder;

    protected $visibleColumn;

    protected $key;

    public function __construct(LazyRenderable $table)
    {
        $this->dialog = DialogTable::make($table);
        $this->id = 'select-table-filter-'.Str::random(8);
    }

    /**
     * 设置选中的选项.
     *
     * @param  \Closure  $options
     * @return $this
     */
    public function options(\Closure $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * 设置选中数据显示.
     *
     * @param  string  $model
     * @param  string  $id
     * @param  string  $text
     * @return $this
     */
    public function model(string $model, string $id = 'id', string $text = 'title')
    {
        return $this->pluck($text, $id)->options(function ($v) use ($model, $id, $text) {
            if (! $v) {
                return [];
            }

            return $model::query()->whereIn($id, $v)->pluck($text, $id);
        });
    }

    /**
     * 设置选中的key以及标题字段.
     *
     * @param $visibleColumn
     * @param $key
     * @return $this
     */
    public function pluck(?string $visibleColumn, ?string $key = 'id')
    {
        $this->visibleColumn = $visibleColumn;
        $this->key = $key;

        return $this;
    }

    /**
     * 设置弹窗宽度.
     *
     * @example
     *    $this->width('500px');
     *    $this->width('50%');
     *
     * @param  string  $width
     * @return $this
     */
    public function dialogWidth(string $width)
    {
        $this->dialog->width($width);

        return $this;
    }

    /**
     * 设置弹窗标题.
     *
     * @param  string  $title
     * @return $this
     */
    public function title($title)
    {
        $this->dialog->title($title);

        return $this;
    }

    /**
     * @param  string  $placeholder
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

    protected function setUpTable()
    {
        $this->dialog
            ->footer($this->renderFooter())
            ->button($this->renderButton());

        // 设置选中的字段和待显示的标题字段
        $this->dialog
            ->getTable()
            ->getRenderable()
            ->payload([
                LazyRenderable::ROW_SELECTOR_COLUMN_NAME => [$this->key, $this->visibleColumn],
            ]);
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

        $this->options = $values;
    }

    protected function addScript()
    {
        $options = json_encode($this->options);

        Admin::script(
            <<<JS
Dcat.init('#{$this->id}', function (self) {
    var dialogId = self.parent().find('{$this->dialog->getElementSelector()}').attr('id');
    
    Dcat.grid.SelectTable({
        dialog: '[data-id="' + dialogId + '"]',
        container: '#{$this->id}',
        input: '#hidden-{$this->id}',
        values: {$options},
    });
})
JS
        );
    }

    /**
     * @return array
     */
    public function defaultVariables(): array
    {
        $this->formatOptions();
        $this->setUpTable();

        $dialog = $this->dialog->render();

        $this->addScript();

        return [
            'id'          => $this->id,
            'dialog'      => $dialog,
            'placeholder' => $this->placeholder(),
        ];
    }

    protected function renderButton()
    {
        return <<<HTML
<div class="btn btn-{$this->style} btn-sm">
    &nbsp;<i class="feather icon-arrow-up"></i>&nbsp;
</div>
HTML;
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
<button class="btn btn-primary btn-sm submit-btn" style="color: #fff">&nbsp;{$submit}&nbsp;</button>&nbsp;
<button class="btn btn-white btn-sm cancel-btn">&nbsp;{$cancel}&nbsp;</button>
HTML;
    }
}

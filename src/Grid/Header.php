<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Widgets\Widget;
use Dcat\Admin\Grid;

class Header extends Widget
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $columnNames = [];

    /**
     * @var array
     */
    protected $html = [];

    public function __construct(Grid $grid, string $label, array $columnNames)
    {
        $this->grid = $grid;
        $this->label = admin_trans_field($label);
        $this->columnNames = $columnNames;

        $this->setupAttributes();
    }

    /**
     * @return array
     */
    public function getColumnNames()
    {
        return $this->columnNames;
    }

    /**
     * 默认隐藏字段
     * 开启responsive模式有效
     *
     * @return $this
     */
    public function hide()
    {
        return $this->responsive(0);
    }

    public function getLabel()
    {
        return $this->label;
    }

    /**
     * 允许使用responsive
     * 开启responsive模式有效
     *
     * data-priority=”1″ 保持可见，但可以在下拉列表筛选隐藏。
     * data-priority=”2″ 480px 分辨率以下可见
     * data-priority=”3″ 640px 以下可见
     * data-priority=”4″ 800px 以下可见
     * data-priority=”5″ 960px 以下可见
     * data-priority=”6″ 1120px 以下可见
     *
     * @param int $priority
     * @return $this
     */
    public function responsive(int $priority = 1)
    {
        $this->setHtmlAttribute('data-priority', $priority);

        return $this;
    }

    /**
     *
     * @param string $html
     * @return $this
     */
    public function append($html)
    {
        $this->html[] = $html;

        return $this;
    }

    protected function setupAttributes()
    {
        $count = count($this->columnNames);
        if ($count == 1) {
            $this->htmlAttributes['rowspan'] = 2;
        } else {
            $this->htmlAttributes['colspan'] = $count;
        }
    }

    public function render()
    {
        $headers = implode(' ', $this->html);

        return "<th {$this->formatHtmlAttributes()}>{$this->label}{$headers}</th>";
    }
}

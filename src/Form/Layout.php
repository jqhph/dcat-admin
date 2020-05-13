<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Layout\Column;

class Layout
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Column[]
     */
    protected $columns = [];

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * @param int   $width   1~12
     * @param mixed $content
     */
    public function column(int $width, $content)
    {
        $width = $width < 1 ? round(12 * $width) : $width;

        $column = new Column($content, $width);

        $this->columns[] = $column;
    }

    /**
     * @param int   $width
     * @param mixed $content
     */
    public function prepend(int $width, $content)
    {
        $width = $width < 1 ? round(12 * $width) : $width;

        $column = new Column($content, $width);

        array_unshift($this->columns, $column);
    }

    /**
     * @param \Closure|null $callback
     *
     * @return BlockForm
     */
    public function form(\Closure $callback = null)
    {
        $form = new Form\BlockForm($this->form);

        $this->form->builder()->addForm($form);

        if ($callback) {
            $callback($form);
        }

        return $form;
    }

    /**
     * Build html of content.
     *
     * @param  bool  $isAlignCenter
     * @return string
     */
    public function build($isAlignCenter = false)
    {
        $html = $alignCenter ? '<div class="row justify-content-md-center">' : '<div class="row">';

        foreach ($this->columns as $column) {
            $html .= $column->render();
        }

        return $html.'</div>';
    }
}

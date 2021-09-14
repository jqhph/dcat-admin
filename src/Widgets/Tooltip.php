<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

class Tooltip extends Widget
{
    protected static $style = '.tooltip-inner{padding:7px 13px;border-radius:2px;font-size:13px;max-width:250px}';

    protected $selector;

    protected $title;

    protected $background;

    protected $maxWidth = 210;

    protected $placement = 1;

    protected $built;

    public function __construct(string $selector = '')
    {
        $this->selector($selector);

        $this->autoRender();
    }

    public function selector(string $selector)
    {
        $this->selector = $selector;

        return $this;
    }

    public function maxWidth(int $width)
    {
        $this->maxWidth = $width;

        return $this;
    }

    /**
     * @param  string|Renderable|\Closure  $content
     * @return $this
     */
    public function title($content)
    {
        $this->title = $this->toString($content);

        return $this;
    }

    public function background(string $color)
    {
        $this->background = $color;

        return $this;
    }

    public function green()
    {
        return $this->background(Admin::color()->success());
    }

    public function blue()
    {
        return $this->background(Admin::color()->blue());
    }

    public function red()
    {
        return $this->background(Admin::color()->danger());
    }

    public function purple()
    {
        return $this->background(Admin::color()->purple());
    }

    public function left()
    {
        return $this->placement('left');
    }

    public function right()
    {
        return $this->placement('right');
    }

    public function top()
    {
        return $this->placement('top');
    }

    public function bottom()
    {
        return $this->placement('bottom');
    }

    public function placement(string $placement = 'auto')
    {
        $map = [
            'top'    => 1,
            'right'  => 2,
            'bottom' => 3,
            'left'   => 4,
        ];

        $this->placement = $map[$placement] ?? 1;

        return $this;
    }

    protected function addScript()
    {
        $background = $this->background ?: Admin::color()->primary(-5);
        $title = $this->title;

        Admin::script(
            <<<JS
$('{$this->selector}').on('mouseover', function () {
    var title = '{$title}' || $(this).data('title');
    var idx = layer.tips(title, this, {
      tips: ['{$this->placement}', '{$background}'],
      time: 0,
      maxWidth: {$this->maxWidth},
    });
    
    $(this).attr('layer-idx', idx);
}).on('mouseleave', function () {
    layer.close($(this).attr('layer-idx'));
    
    $(this).attr('layer-idx', '');
});
JS
        );
    }

    public function render()
    {
        if ($this->built) {
            return;
        }
        $this->built = true;

        $this->addScript();
    }
}

<?php

namespace Dcat\Admin\Grid\Column;

use Dcat\Admin\Widgets\Tooltip;
use Illuminate\Contracts\Support\Renderable;

class Help implements Renderable
{
    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var string
     */
    protected $style;

    /**
     * Help constructor.
     *
     * @param string $message
     */
    public function __construct($message = '', $style = null)
    {
        $this->message = $message;
        $this->style = null;
    }

    /**
     * Render help  header.
     *
     * @return string
     */
    public function render()
    {
        $tooltip = Tooltip::make('.grid-column-help');

        if ($this->style && method_exists($tooltip, $this->style)) {
            $tooltip->{$this->style};
        }

        $tooltip->content($this->message)
            ->top()
            ->render();

        return <<<HELP
<a href="javascript:void(0);" class="grid-column-help" >
    <i class="fa fa-question-circle"></i>
</a>
HELP;
    }

}

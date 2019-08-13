<?php

namespace Dcat\Admin\Grid\Column;

use Dcat\Admin\Widgets\Tooltip;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

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
        $random = Str::random(8);

        $tooltip = Tooltip::make('.grid-column-help-'.$random);

        if ($this->style && method_exists($tooltip, $this->style)) {
            $tooltip->{$this->style};
        }

        $tooltip->content($this->message)
            ->bottom()
            ->render();

        return <<<HELP
<a href="javascript:void(0);" class="grid-column-help-{$random}" >
    <i class="fa fa-question-circle"></i>
</a>
HELP;
    }

}

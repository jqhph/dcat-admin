<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;

class Html extends Field
{
    /**
     * Htmlable.
     *
     * @var string|\Closure
     */
    protected $html = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var bool
     */
    protected $plain = false;

    /**
     * Create a new Html instance.
     *
     * @param mixed $html
     * @param array $arguments
     */
    public function __construct($html, $arguments)
    {
        $this->html = $html;

        $this->label = Arr::get($arguments, 0);
    }

    /**
     * @return $this
     */
    public function plain()
    {
        $this->plain = true;

        return $this;
    }

    /**
     * Render html field.
     *
     * @return string
     */
    public function render()
    {
        if ($this->html instanceof \Closure) {
            $this->html = Helper::render(
                $this->html->call($this->values(), $this->form)
            );
        }

        if ($this->plain) {
            return $this->html;
        }

        $viewClass = $this->getViewElementClasses();

        return <<<EOT
<div class="{$viewClass['form-group']}">
    <label  class="{$viewClass['label']} control-label">{$this->label}</label>
    <div class="{$viewClass['field']}">
        {$this->html}
    </div>
</div>
EOT;
    }
}

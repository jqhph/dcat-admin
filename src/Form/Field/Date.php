<?php

namespace Dcat\Admin\Form\Field;

class Date extends Text
{
    public static $js = '@bootstrap-datetimepicker';
    public static $css = '@bootstrap-datetimepicker';

    protected $format = 'yyyy-mm-dd';

    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    protected function prepareInputValue($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value;
    }

    public function render()
    {
        $this->options['format'] = $this->format;
        $this->options['locale'] = config('app.locale');
        $this->options['allowInputToggle'] = true;

        $this->script = "$('{$this->getElementClassSelector()}').datetimepicker(".json_encode($this->options).');';

        $this->prepend('<i class="fa fa-calendar fa-fw"></i>')
            ->defaultAttribute('style', 'width: 200px');

        return parent::render();
    }
}

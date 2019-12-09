<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

class Date extends Text
{
    protected $format = 'YYYY-MM-DD';

    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    protected function prepareToSave($value)
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

        $this->script = "$('{$this->elementClassSelector()}').parent().datetimepicker(".json_encode($this->options).');';

        $this->prepend('<i class="fa fa-calendar fa-fw"></i>')
            ->defaultAttribute('style', 'width: 200px');

        return parent::render();
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('moment');
        Admin::collectComponentAssets('bootstrap-datetimepicker');
    }
}

<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

/**
 * Class ListBox.
 *
 * @see https://github.com/istvan-ujjmeszaros/bootstrap-duallistbox
 */
class Listbox extends MultipleSelect
{
    protected $settings = [];

    public function settings(array $settings)
    {
        $this->settings = $settings;

        return $this;
    }

    public function render()
    {
        $settings = array_merge($this->settings, [
            'infoText'          => trans('admin.listbox.text_total'),
            'infoTextEmpty'     => trans('admin.listbox.text_empty'),
            'infoTextFiltered'  => trans('admin.listbox.filtered'),
            'filterTextClear'   => trans('admin.listbox.filter_clear'),
            'filterPlaceHolder' => trans('admin.listbox.filter_placeholder'),
        ]);

        $settings = json_encode($settings);

        $this->script = <<<JS
$("{$this->getElementClassSelector()}").bootstrapDualListbox($settings);
JS;

        return parent::render();
    }

    public static function collectAssets()
    {
        Admin::collectAssets('jquery.bootstrap-duallistbox');
    }
}

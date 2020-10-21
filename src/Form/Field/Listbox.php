<?php

namespace Dcat\Admin\Form\Field;

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

        $this->addVariables(['settings' => $settings]);

        return parent::render();
    }
}

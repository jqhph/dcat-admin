<?php

namespace Dcat\Admin\Http\Displayers\Extensions;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Http\Actions\Extensions\Update;
use Dcat\Admin\Widgets\Modal;

class Description extends AbstractDisplayer
{
    public function display()
    {
        return Admin::view('admin::grid.displayer.extensions.description', [
            'value' => $this->value,
            'row'   => $this->row,
            'settingAction' => $this->resolveSettingForm(),
            'updateAction' => $this->resolveAction(Update::class),
        ]);
    }

    protected function resolveSettingForm()
    {
        $extension = Admin::extension()->get($this->getKey());

        if (! method_exists($extension, 'settingForm')) {
            return;
        }

        $label = trans('admin.setting');

        return Modal::make()
            ->lg()
            ->title($this->getModalTitle($extension))
            ->body($extension->settingForm())
            ->button($label);
    }

    protected function getModalTitle($extension)
    {
        return $extension->settingForm()->title()
            ?: (trans('admin.setting').' - '.str_replace('.', '/', $this->getKey()));
    }

    protected function resolveAction($action)
    {
        $action = new $action();

        $action->setGrid($this->grid);
        $action->setColumn($this->column);
        $action->setRow($this->row);

        return $action->render();
    }
}

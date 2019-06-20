<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;

class FilterButton extends AbstractTool
{
    /**
     * @var string
     */
    protected $view = 'admin::filter.button';

    /**
     * @var string
     */
    protected $btnClassName;

    /**
     * @return \Dcat\Admin\Grid\Filter
     */
    protected function filter()
    {
        return $this->grid->getFilter();
    }

    /**
     * Get button class name.
     *
     * @return string
     */
    protected function getElementClassName()
    {
        if (!$this->btnClassName) {
            $this->btnClassName = uniqid().'-filter-btn';
        }

        return $this->btnClassName;
    }

    /**
     * Set up script for filter button.
     */
    protected function setupScripts()
    {
        $id = $this->filter()->getFilterID();

        Admin::script(<<<JS
$('.{$this->getElementClassName()}').click(function(){
    $('#{$id}').parent().collapse('toggle');
}); 
JS
        );
    }

    /**
     * @return mixed
     */
    protected function renderScopes()
    {
        return $this->filter()->getScopes()->map->render()->implode("\r\n");
    }

    /**
     * Get label of current scope.
     *
     * @return string
     */
    protected function getCurrentScopeLabel()
    {
        if ($scope = $this->filter()->getCurrentScope()) {
            return "&nbsp;{$scope->getLabel()}&nbsp;";
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $filter = $this->filter();

        $scopres = $filter->getScopes();
        $filters = $filter->filters();

        if ($scopres->isEmpty() && !$filters) {
            return;
        }

        $this->setupScripts();

        $showText = ((!$filters || $this->grid->option('show_filter') === false) && !$scopres->isEmpty()) ? true : false;

        $variables = [
            'scopes'           => $scopres,
            'current_label'    => $this->getCurrentScopeLabel(),
            'url_no_scopes'    => $filter->urlWithoutScopes(),
            'btn_class'        => $this->getElementClassName(),
            'expand'           => $filter->expand,
            'show_filter_text' => $showText,
        ];

        return view($this->view, $variables)->render();
    }
}

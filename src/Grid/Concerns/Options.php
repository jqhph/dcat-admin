<?php

namespace Dcat\Admin\Grid\Concerns;

trait Options
{
    /**
     * Options for grid.
     *
     * @var array
     */
    protected $options = [
        'show_pagination'        => true,
        'show_filter'            => true,
        'show_exporter'          => false,
        'show_export_all'        => true,
        'show_actions'           => true,
        'show_quick_edit_button' => true,
        'show_edit_button'       => true,
        'show_view_button'       => true,
        'show_delete_button'     => true,
        'show_row_selector'      => true,
        'show_create_btn'        => true,
        'show_quick_create_btn'  => true,
        'show_bordered'          => false,
        'show_toolbar'           => true,

        'row_selector_style'      => 'primary',
        'row_selector_circle'     => true,
        'row_selector_clicktr'    => false,
        'row_selector_label_name' => null,
        'row_selector_bg'         => 'var(--20)',

        'export_limit'       => 50000,
        'dialog_form_area'   => ['700px', '670px'],
        'table_header_style' => 'table-header-default',

    ];

    /**
     * Get or set option for grid.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this|mixed
     */
    public function option($key, $value = null)
    {
        if (is_null($value)) {
            return $this->options[$key];
        }

        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Disable grid pagination.
     *
     * @return $this
     */
    public function disablePagination(bool $disable = true)
    {
        $this->model->usePaginate(!$disable);

        return $this->option('show_pagination', !$disable);
    }

    /**
     * Show grid pagination.
     *
     * @param bool $val
     * @return $this
     */
    public function showPagination(bool $val = true)
    {
        return $this->disablePagination(!$val);
    }

    /**
     * Disable all actions.
     *
     * @return $this
     */
    public function disableActions(bool $disable = true)
    {
        return $this->option('show_actions', !$disable);
    }

    /**
     * Show all actions.
     *
     * @return $this
     */
    public function showActions(bool $val = true)
    {
        return $this->disableActions(!$val);
    }

    /**
     * Disable row selector.
     *
     * @return $this
     */
    public function disableRowSelector(bool $disable = true)
    {
        $this->tools->disableBatchActions($disable);

        return $this->option('show_row_selector', !$disable);
    }

    /**
     * Show row selector.
     *
     * @return $this
     */
    public function showRowSelector(bool $val = true)
    {
        return $this->disableRowSelector(!$val);
    }

    /**
     * Disable grid filter.
     *
     * @return $this
     */
    public function disableFilter(bool $disable = true)
    {
//        $this->tools->disableFilterButton($disable);
        $this->filter->disableCollapse($disable);

        return $this->option('show_filter', !$disable);
    }

    /**
     * Show grid filter.
     *
     * @param bool $val
     * @return $this
     */
    public function showFilter(bool $val = true)
    {
        return $this->disableFilter(!$val);
    }

    /**
     * Disable refresh button.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableRefreshButton(bool $disable = true)
    {
        $this->tools->disableRefreshButton($disable);

        return $this;
    }

    /**
     * Show refresh button.
     *
     * @param bool $val
     * @return $this
     */
    public function showRefreshButton(bool $val = true)
    {
        return $this->disableRefreshButton(!$val);
    }

    /**
     * Disable filter button.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableFilterButton(bool $disable = true)
    {
        $this->tools->disableFilterButton($disable);

        return $this;
    }

    /**
     * Show filter button.
     *
     * @param bool $val
     * @return $this
     */
    public function showFilterButton(bool $val = true)
    {
        return $this->disableFilterButton(!$val);
    }

    /**
     * Disable batch actions.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableBatchActions(bool $disable = true)
    {
        $this->tools->disableBatchActions($disable);

        return $this;
    }

    /**
     * Show batch actions.
     *
     * @param bool $val
     * @return $this
     */
    public function showBatchActions(bool $val = true)
    {
        return $this->disableBatchActions(!$val);
    }

    /**
     * Disable batch delete.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableBatchDelete(bool $disable = true)
    {
        $this->tools->batch(function ($action) use ($disable) {
            $action->disableDelete($disable);
        });

        return $this;
    }

    /**
     * Show batch delete.
     *
     * @param bool $val
     * @return $this
     */
    public function showBatchDelete(bool $val = true)
    {
        return $this->disableBatchDelete(!$val);
    }

    /**
     * Disable edit.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableEditButton(bool $disable = true)
    {
        $this->options['show_edit_button'] = !$disable;

        return $this;
    }

    /**
     * Show edit.
     *
     * @param bool $val
     * @return $this
     */
    public function showEditButton(bool $val = true)
    {
        return $this->disableEditButton(!$val);
    }

    /**
     * Disable quick edit.
     *
     * @return $this.
     */
    public function disableQuickEditButton(bool $disable = true)
    {
        $this->options['show_quick_edit_button'] = !$disable;

        return $this;
    }

    /**
     * Show quick edit button.
     *
     * @return $this.
     */
    public function showQuickEditButton(bool $val = true)
    {
        return $this->disableQuickEditButton(!$val);
    }

    /**
     * Disable view action.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableViewButton(bool $disable = true)
    {
        $this->options['show_view_button'] = !$disable;

        return $this;
    }

    /**
     * Show view action.
     *
     * @param bool $disable
     * @return $this
     */
    public function showViewButton(bool $val = true)
    {
        return $this->disableViewButton(!$val);
    }

    /**
     * Disable delete.
     *
     * @param bool $disable
     * @return $this
     */
    public function disableDeleteButton(bool $disable = true)
    {
        $this->options['show_delete_button'] = !$disable;

        return $this;
    }

    /**
     * Show delete button.
     *
     * @param bool $disable
     * @return $this
     */
    public function showDeleteButton(bool $val = true)
    {
        return $this->disableDeleteButton(!$val);
    }

    /**
     * Disable export.
     *
     * @return $this
     */
    public function disableExporter(bool $disable = true)
    {
        return $this->option('show_exporter', !$disable);
    }

    /**
     * Show export button.
     *
     * @return $this
     */
    public function showExporter(bool $val = true)
    {
        return $this->disableExporter(!$val);
    }

    /**
     * Disable export all.
     *
     * @return $this
     */
    public function disableExporterAll(bool $disable = true)
    {
        return $this->option('show_export_all', !$disable);
    }

    /**
     * Show export all option.
     *
     * @return $this
     */
    public function showExportAll(bool $val = true)
    {
        return $this->disableExporterAll(!$val);
    }

    /**
     * Remove create button on grid.
     *
     * @return $this
     */
    public function disableCreateButton(bool $disable = true)
    {
        return $this->option('show_create_btn', !$disable);
    }

    /**
     * Show create button.
     *
     * @return $this
     */
    public function showCreateButton(bool $val = true)
    {
        return $this->disableCreateButton(!$val);
    }

    public function disableQuickCreateButton(bool $disable = true)
    {
        return $this->option('show_quick_create_btn', !$disable);
    }

    public function showQuickCreateButton(bool $val = true)
    {
        return $this->disableQuickCreateButton(!$val);
    }

    public function disableToolbar(bool $val = true)
    {
        return $this->option('show_toolbar', !$val);
    }

    public function showToolbar(bool $val = true)
    {
        return $this->disableToolbar(!$val);
    }

}

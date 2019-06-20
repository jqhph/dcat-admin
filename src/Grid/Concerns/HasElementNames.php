<?php

namespace Dcat\Admin\Grid\Concerns;

trait HasElementNames
{
    /**
     * Grid name.
     *
     * @var string
     */
    protected $__name;

    /**
     * HTML element names.
     *
     * @var array
     */
    protected $elementNames = [
        'grid_row'        => 'grid-row',
        'grid_select_all' => 'grid-select-all',
        'grid_per_page'   => 'grid-per-pager',
        'grid_batch'      => 'grid-batch',
        'export_selected' => 'export-selected',
        'selected_rows'   => 'selectedRows',
    ];

    /**
     * Set name to grid.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->__name = $name;

        $m = $this->model();

        $m->setPerPageName("{$name}_{$m->getPerPageName()}")
            ->setPageName("{$name}_{$m->getPageName()}")
            ->setSortName("{$name}_{$m->getSortName()}");

        $this->getFilter()->setName($name);

        return $this;
    }

    /**
     * Get name of grid.
     *
     * @return string
     */
    public function getName()
    {
        return $this->__name;
    }

    /**
     * @return string
     */
    public function getGridRowName()
    {
        return $this->elementNameWithPrefix('grid_row');
    }

    /**
     * @return string
     */
    public function getSelectAllName()
    {
        return $this->elementNameWithPrefix('grid_select_all');
    }

    /**
     * @return string
     */
    public function getPerPageName()
    {
        return $this->elementNameWithPrefix('grid_per_page');
    }

    /**
     * @return string
     */
    public function getGridBatchName()
    {
        return $this->elementNameWithPrefix('grid_batch');
    }

    /**
     * @return string
     */
    public function getExportSelectedName()
    {
        return $this->elementNameWithPrefix('export_selected');
    }

    /**
     * @return string
     */
    public function getSelectedRowsName()
    {
        $elementName = $this->elementNames['selected_rows'];

        if ($this->__name) {
            return sprintf('%s%s', $this->__name, ucfirst($elementName));
        }

        return $elementName;
    }

    /**
     * @return string
     */
    protected function elementNameWithPrefix($name)
    {
        $elementName = $this->elementNames[$name];

        if ($this->__name) {
            return sprintf('%s-%s', $this->__name, $elementName);
        }

        return $elementName;
    }
}

<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid;

/**
 * @method Grid\Model model()
 */
trait HasNames
{
    /**
     * Grid name.
     *
     * @var string
     */
    protected $_name;

    /**
     * Set name to grid.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;
        $this->tableId = $this->makeName($this->tableId);

        return $this;
    }

    /**
     * Get name of grid.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getRequestInput($key)
    {
        return $this->request->get($this->makeName($key));
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function makeName($key)
    {
        return $this->getNamePrefix().$key;
    }

    /**
     * @return string
     */
    public function getNamePrefix()
    {
        if (! $name = $this->getName()) {
            return;
        }

        return $name.'_';
    }

    /**
     * @return string
     */
    public function getRowName()
    {
        return $this->makeName('grid-row');
    }

    /**
     * @return string
     */
    public function getSelectAllName()
    {
        return $this->makeName('grid-select-all');
    }

    /**
     * @return string
     */
    public function getPerPageName()
    {
        return $this->makeName('grid-per-page');
    }

    /**
     * @return string
     */
    public function getExportSelectedName()
    {
        return $this->makeName('export-selected');
    }
}

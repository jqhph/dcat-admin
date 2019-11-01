<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Exporters\CsvExporter;

class Exporter
{
    /**
     * Export scope constants.
     */
    const SCOPE_ALL = 'all';
    const SCOPE_CURRENT_PAGE = 'page';
    const SCOPE_SELECTED_ROWS = 'selected';

    /**
     * Available exporter drivers.
     *
     * @var array
     */
    protected static $drivers = [];

    /**
     * Export query name.
     *
     * @var string
     */
    protected $queryName = '_export_';

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Create a new Exporter instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Set export query name.
     *
     * @param $name
     *
     * @return $this
     */
    public function setQueryName($name)
    {
        $this->queryName = $name;

        return $this;
    }

    /**
     * Get export query name.
     *
     * @return string
     */
    public function getQueryName(): string
    {
        return $this->queryName;
    }

    /**
     * Extends new exporter driver.
     *
     * @param $driver
     * @param $extend
     */
    public static function extend($driver, $extend)
    {
        static::$drivers[$driver] = $extend;
    }

    /**
     * Resolve export driver.
     *
     * @param string $driver
     *
     * @return CsvExporter
     */
    public function resolve($driver)
    {
        if ($driver instanceof Grid\Exporters\AbstractExporter) {
            return $driver->setGrid($this->grid);
        }

        return $this->getExporter($driver);
    }

    /**
     * Get export driver.
     *
     * @param string $driver
     *
     * @return Grid\Exporters\AbstractExporter
     */
    protected function getExporter($driver)
    {
        if (!array_key_exists($driver, static::$drivers)) {
            return $this->getDefaultExporter();
        }

        return (new static::$drivers[$driver]())->setGrid($this->grid);
    }

    /**
     * Get default exporter.
     *
     * @return Grid\Exporters\AbstractExporter
     */
    public function getDefaultExporter()
    {
        return (new Grid\Exporters\XlsxExporter())->setGrid($this->grid);
    }

    /**
     * Format query for export url.
     *
     * @param int  $scope
     * @param null $args
     *
     * @return array
     */
    public function formatExportQuery($scope = '', $args = null)
    {
        $query = '';

        if ($scope == static::SCOPE_ALL) {
            $query = 'all';
        }

        if ($scope == static::SCOPE_CURRENT_PAGE) {
            $query = "page:$args";
        }

        if ($scope == static::SCOPE_SELECTED_ROWS) {
            $query = "selected:$args";
        }

        return [$this->queryName => $query];
    }
}
